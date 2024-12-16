<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="bg-white shadow-md">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-800">SPIB</h1>
                <nav class="ml-8">
                    <ul class="flex space-x-6">
                        <li>
                            <a href="/JS/SPIB/dashboard/pm.php" class="text-gray-600 hover:text-gray-900">
                                Dashboard
                            </a>
                        </li>
                        <!-- Ajoutez d'autres liens de navigation ici si nécessaire -->
                    </ul>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
                </div>
                <a href="/JS/SPIB/api/auth/logout.php" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</header>
