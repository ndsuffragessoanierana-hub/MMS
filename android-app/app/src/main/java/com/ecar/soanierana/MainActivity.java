package com.ecar.soanierana;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.DownloadManager;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.net.http.SslError;
import android.os.Bundle;
import android.os.Environment;
import android.view.View;
import android.webkit.CookieManager;
import android.webkit.MimeTypeMap;
import android.webkit.SslErrorHandler;
import android.webkit.URLUtil;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

/**
 * Enveloppe (wrapper) WebView pour l'application ECAR Soanierana.
 *
 * Cette activité affiche simplement le site web MMS dans une WebView
 * plein écran, avec :
 *  - une barre de progression pendant le chargement,
 *  - un écran d'erreur avec bouton "Réessayer" en cas de coupure réseau
 *    (utile car l'hébergement Render peut mettre du temps à démarrer),
 *  - le bouton Retour Android qui navigue dans l'historique du site au
 *    lieu de fermer l'application directement,
 *  - le support du choix de fichier (input type="file") pour les pages
 *    qui en ont besoin (ex: import Excel),
 *  - le tirer-pour-actualiser (swipe to refresh),
 *  - le téléchargement des fichiers (PDF, Excel, etc.) via DownloadManager,
 *    avec transmission des cookies de session pour les routes protégées
 *    par authentification.
 */
public class MainActivity extends AppCompatActivity {

    // Adresse du site à afficher. À modifier ici si l'adresse change.
    private static final String SITE_URL = "https://ecar-soanierana.onrender.com";

    private static final int STORAGE_PERMISSION_CODE = 100;

    private WebView webView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private ProgressBar progressBar;
    private View errorView;
    private TextView errorMessage;

    private ValueCallback<Uri[]> filePathCallback;

    // Conserve la dernière tentative de téléchargement si on doit d'abord
    // demander la permission d'écriture (Android <= 9).
    private String pendingDownloadUrl;
    private String pendingDownloadUserAgent;
    private String pendingDownloadContentDisposition;
    private String pendingDownloadMimeType;

    private final androidx.activity.result.ActivityResultLauncher<Intent> fileChooserLauncher =
        registerForActivityResult(
            new androidx.activity.result.contract.ActivityResultContracts.StartActivityForResult(),
            result -> {
                if (filePathCallback == null) {
                    return;
                }

                Uri[] results = null;

                if (result.getResultCode() == RESULT_OK && result.getData() != null) {
                    Uri data = result.getData().getData();
                    if (data != null) {
                        results = new Uri[]{data};
                    }
                }

                filePathCallback.onReceiveValue(results);
                filePathCallback = null;
            }
        );

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webView);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        progressBar = findViewById(R.id.progressBar);
        errorView = findViewById(R.id.errorView);
        errorMessage = findViewById(R.id.errorMessage);

        Button retryButton = findViewById(R.id.retryButton);
        retryButton.setOnClickListener(v -> {
            errorView.setVisibility(View.GONE);
            webView.setVisibility(View.VISIBLE);
            webView.reload();
        });

        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);
        settings.setLoadWithOverviewMode(true);
        settings.setUseWideViewPort(true);
        settings.setSupportZoom(true);
        settings.setBuiltInZoomControls(true);
        settings.setDisplayZoomControls(false);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);
        settings.setCacheMode(WebSettings.LOAD_DEFAULT);

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                Uri uri = request.getUrl();
                String scheme = uri.getScheme();

                // Les liens mailto:, tel:, etc. sont ouverts par une
                // application externe plutôt que dans la WebView.
                if (scheme != null && !scheme.equals("http") && !scheme.equals("https")) {
                    try {
                        startActivity(new Intent(Intent.ACTION_VIEW, uri));
                    } catch (ActivityNotFoundException e) {
                        // Aucune application ne gère ce lien : on ignore simplement.
                    }
                    return true;
                }

                // Tout le reste (le site lui-même) reste dans la WebView.
                return false;
            }

            @Override
            public void onPageStarted(WebView view, String url, android.graphics.Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                progressBar.setVisibility(View.VISIBLE);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressBar.setVisibility(View.GONE);
                swipeRefreshLayout.setRefreshing(false);
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                super.onReceivedError(view, request, error);

                // On n'affiche l'écran d'erreur que pour la page principale,
                // pas pour une ressource secondaire (image, police, etc.)
                // qui a échoué à charger.
                if (request.isForMainFrame()) {
                    showError();
                }
            }

            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                // Par sécurité, on refuse les certificats invalides plutôt
                // que de les accepter silencieusement.
                handler.cancel();
                showError();
            }
        });

        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                super.onProgressChanged(view, newProgress);
                progressBar.setProgress(newProgress);
            }

            // Support du choix de fichier pour les <input type="file">
            // (ex: import Excel depuis le site).
            @Override
            public boolean onShowFileChooser(WebView webView, ValueCallback<Uri[]> callback,
                                             FileChooserParams fileChooserParams) {
                if (filePathCallback != null) {
                    filePathCallback.onReceiveValue(null);
                }
                filePathCallback = callback;

                Intent intent = fileChooserParams.createIntent();
                try {
                    fileChooserLauncher.launch(intent);
                } catch (ActivityNotFoundException e) {
                    filePathCallback = null;
                    return false;
                }

                return true;
            }
        });

        // Gestion des téléchargements (PDF, Excel, etc.) déclenchés par le site,
        // que ce soit via Pdf::download() (Content-Disposition: attachment)
        // ou un lien direct vers un fichier.
        webView.setDownloadListener((url, userAgent, contentDisposition, mimetype, contentLength) -> {
            if (hasStoragePermission()) {
                startDownload(url, userAgent, contentDisposition, mimetype);
            } else {
                pendingDownloadUrl = url;
                pendingDownloadUserAgent = userAgent;
                pendingDownloadContentDisposition = contentDisposition;
                pendingDownloadMimeType = mimetype;
                requestStoragePermission();
            }
        });

        swipeRefreshLayout.setOnRefreshListener(() -> webView.reload());

        if (savedInstanceState != null) {
            webView.restoreState(savedInstanceState);
        } else {
            webView.loadUrl(SITE_URL);
        }

        getOnBackPressedDispatcher().addCallback(this, new androidx.activity.OnBackPressedCallback(true) {
            @Override
            public void handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack();
                } else {
                    setEnabled(false);
                    getOnBackPressedDispatcher().onBackPressed();
                }
            }
        });
    }

    /**
     * Sur Android 9 (API 28) et en dessous, l'écriture dans le dossier
     * public "Téléchargements" nécessite la permission WRITE_EXTERNAL_STORAGE
     * accordée explicitement par l'utilisateur.
     * À partir d'Android 10, DownloadManager peut écrire dans ce dossier
     * sans cette permission (scoped storage), donc on considère qu'elle
     * est acquise.
     */
    private boolean hasStoragePermission() {
        if (android.os.Build.VERSION.SDK_INT > android.os.Build.VERSION_CODES.P) {
            return true;
        }
        return ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE)
            == PackageManager.PERMISSION_GRANTED;
    }

    private void requestStoragePermission() {
        ActivityCompat.requestPermissions(this,
            new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE},
            STORAGE_PERMISSION_CODE);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == STORAGE_PERMISSION_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED
                && pendingDownloadUrl != null) {
                startDownload(pendingDownloadUrl, pendingDownloadUserAgent,
                    pendingDownloadContentDisposition, pendingDownloadMimeType);
            } else {
                Toast.makeText(this,
                    "Permission refusée : impossible de télécharger le fichier.",
                    Toast.LENGTH_LONG).show();
            }
            pendingDownloadUrl = null;
            pendingDownloadUserAgent = null;
            pendingDownloadContentDisposition = null;
            pendingDownloadMimeType = null;
        }
    }

    private void startDownload(String url, String userAgent, String contentDisposition, String mimetype) {
        try {
            String fileName = URLUtil.guessFileName(url, contentDisposition, mimetype);

            DownloadManager.Request request = new DownloadManager.Request(Uri.parse(url));
            request.setMimeType(mimetype);

            // On transmet les cookies de session : indispensable si la route
            // PDF est protégée par le middleware "auth" de Laravel, sinon
            // DownloadManager fait une requête anonyme qui échoue (ou
            // récupère la page de login au lieu du PDF).
            String cookies = CookieManager.getInstance().getCookie(url);
            if (cookies != null) {
                request.addRequestHeader("cookie", cookies);
            }
            request.addRequestHeader("User-Agent", userAgent);

            request.setDescription("Téléchargement en cours...");
            request.setTitle(fileName);
            request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
            request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName);
            request.allowScanningByMediaScanner();

            DownloadManager dm = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
            if (dm != null) {
                dm.enqueue(request);
                Toast.makeText(getApplicationContext(),
                    "Téléchargement démarré : " + fileName, Toast.LENGTH_LONG).show();
            }
        } catch (Exception e) {
            Toast.makeText(getApplicationContext(),
                "Impossible de télécharger le fichier.", Toast.LENGTH_LONG).show();
        }
    }

    private void showError() {
        progressBar.setVisibility(View.GONE);
        swipeRefreshLayout.setRefreshing(false);
        webView.setVisibility(View.GONE);
        errorView.setVisibility(View.VISIBLE);
        errorMessage.setText(R.string.connection_error);
    }

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);
        webView.saveState(outState);
    }
}
