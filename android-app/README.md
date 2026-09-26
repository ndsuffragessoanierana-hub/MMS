# ECAR Soanierana — application Android (WebView)

Ce dossier est un projet Android Studio prêt à ouvrir. Il affiche le site
**https://ecar-soanierana.onrender.com** dans une WebView plein écran,
avec :

- une barre de progression pendant le chargement,
- un écran "Réessayer" en cas de coupure réseau (utile car l'hébergement
  Render gratuit peut mettre 30-60s à démarrer après une période
  d'inactivité),
- le bouton Retour Android qui navigue dans l'historique du site plutôt
  que de fermer l'application,
- le support des `<input type="file">` (ex: import Excel) via le
  sélecteur de fichier natif,
- le tirer-pour-actualiser (swipe to refresh).

## Comment obtenir le fichier .apk

1. Installe [Android Studio](https://developer.android.com/studio) si ce
   n'est pas déjà fait (gratuit).
2. Ouvre Android Studio → **File > Open** → sélectionne ce dossier
   (`android-app`).
3. Laisse Android Studio synchroniser le projet (Gradle Sync) — ça
   télécharge automatiquement les dépendances la première fois
   (connexion internet nécessaire).
   - Si Android Studio signale l'absence d'un "Gradle Wrapper" complet,
     laisse-le en créer un (bouton proposé automatiquement), ou utilise
     **File > Settings > Build Tools > Gradle** pour pointer vers une
     installation Gradle locale (une installation Gradle standard,
     version 8.7, fonctionne).
4. Une fois la synchronisation terminée : **Build > Build Bundle(s) /
   APK(s) > Build APK(s)**.
5. Le fichier `.apk` généré se trouve dans
   `app/build/outputs/apk/debug/app-debug.apk`. C'est ce fichier que tu
   peux installer sur un téléphone Android (active "Sources inconnues"
   dans les paramètres du téléphone pour l'installer manuellement).

## Modifier l'adresse du site

L'adresse est définie en une seule ligne, en haut du fichier
`app/src/main/java/com/ecar/soanierana/MainActivity.java` :

```java
private static final String SITE_URL = "https://ecar-soanierana.onrender.com";
```

## Icône et nom de l'application

- Nom affiché : `ECAR Soanierana` (modifiable dans
  `app/src/main/res/values/strings.xml`, clé `app_name`).
- Icône : générée automatiquement (croix blanche sur fond bleu), dans
  `app/src/main/res/mipmap-*/ic_launcher.png`. Pour la remplacer par un
  vrai logo, utilise l'outil intégré d'Android Studio : clic droit sur
  `res` → **New > Image Asset**.

## Publier sur le Play Store (optionnel, plus tard)

Le build ci-dessus produit un APK "debug", suffisant pour tester sur un
téléphone. Pour publier sur le Play Store, il faudra générer un
**Android App Bundle** signé avec une clé de production
(**Build > Generate Signed Bundle / APK**) — une étape distincte, à
faire quand tu seras prêt à publier.
