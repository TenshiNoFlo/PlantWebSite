<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?= $cakeDescription = 'Pediz Company'?>:
        </title>
        <?= $this->Html->meta('icon') ?>

        <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>
        <?= $this->Html->css(['style']) ?>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body>

        <div class="sidebar">
            <?php
            if ($this->request->getAttribute('identity')) {
                $userProfileImage = 'userDefault.jpg';
                $userEmail = $this->request->getAttribute('identity')->get('email');
                echo '<div class="user-info">';
                echo '<img class="profile-image" src="' . $this->Url->image($userProfileImage) . '" alt="Profile Image">';
                echo '<div class="sidebar-item">' . $userEmail . '</div>';
                echo '</div>';
            }
            ?>
            <?= $this->Html->link('✿ Plantes', ['controller' => 'Plants', 'action' => 'index']) ?>
            <?= $this->Html->link('✿ Ajouter une plante', ['controller' => 'Plants', 'action' => 'add']) ?>
            <?= $this->Html->link('✿ Users', ['controller' => 'Users', 'action' => 'index']) ?>

        </div>

        <!-- Contenu principal -->
        <main class="main" style="margin-left: 250px;">
            <nav class="top-nav">
                <div class="top-nav-title">
                    <a><span>PedizPlant</span>Company</a>
                </div>
                <div class="top-nav-links">
                    <?php
                    if ($this->request->getAttribute('identity')) {
                        echo $this->Html->link("Se déconnecter", ['controller' => 'Users', 'action' => 'logout']);
                    } else {
                        echo $this->Html->link("Se connecter", ['controller' => 'Users', 'action' => 'login']);
                    }
                    ?>
                </div>
            </nav>
            
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </main>
    </body>
</html>
