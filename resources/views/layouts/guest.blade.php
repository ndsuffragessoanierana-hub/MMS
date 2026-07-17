<x-app-layout>
    <div class="font-sans text-gray-900 antialiased">
        <!-- Fond d'écran plein écran avec un léger dégradé pour rendre la page vivante -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-100 via-white to-indigo-50">
            
            <!-- LE LOGO LARAVEL A ÉTÉ SUPPRIMÉ D'ICI -->

            <!-- On injecte directement le contenu de ton login.blade.php -->
            {{ $slot }}
            
        </div>
    </div>
</x-app-layout>