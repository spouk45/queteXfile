<?php include('inc/head.php');

//  ------------- Bon courage pour tout lire !! ---------------


$root = 'files';
$currentFile = '';
$lastDir = '';

if(isset($_POST['editFile'])){
    $fileToOpen = fopen($root.$_GET['open'],'r+');
    fwrite($fileToOpen,$_POST['editFile']);
    fclose($fileToOpen);
}

if (!empty($_GET['open']) && !isset($_POST['editFile'])){
    $info = new SplFileInfo($root.$_GET['open']);

    if($info->getExtension() == 'txt' || $info->getExtension() == 'html'){
        $fileToOpen = fopen($root.$_GET['open'],'r+');

        ?>
        <form method="POST" action="">
            <input type="hidden" name="file" value="<?= $_GET['open']?>">
            <textarea name="editFile" cols="90" rows="20">
                <?php  echo fread($fileToOpen,filesize($root.$_GET['open']));?>
            </textarea>
            <input type="submit" value="Modifier">
        </form><?php

        fclose($fileToOpen);
    }
}

if (!empty($_GET['delete']) && $_GET['delete'] != '..') {
    if (is_dir($dirToDelete = $root . $_GET['delete'])) {
        $files = scandir($dirToDelete);

        foreach ($files as $file) {
            if (file_exists($fileToDelete = $root .$_GET['delete'].'/'. $file) && $file != '..' && $file != '.') {
                if(is_dir($fileToDelete)){
                    echo 'Impossible de supprimer ce répertoire: d\'autres répertoires existent dans celui -ci.';
                    echo '<br><a href="/">Retour</a>';
                    exit();
                }
                else{
                    unlink($fileToDelete);
                }
            }
        }
        rmdir($dirToDelete);
    }

        if (file_exists($fileToDelete = $root . $_GET['delete'])) {
            unlink($fileToDelete);
            //header('location:/');
        }

}

if (!empty($_GET['file']) && $_GET['file'] != '..') {
    $currentFile .= $_GET['file'];
    $tab = explode('/', $currentFile);
    if (count($tab) > 1) {
        end($tab);
        unset($tab[key($tab)]);

        $lastDir = implode('/', $tab);
    }
}


$dir = $root . $currentFile;
if ($dir == 'files//..') {
    $dir = $root;
    $currentFile = '';
}
$files = scandir($dir);
sort($files);
echo '<ul>';
foreach ($files as $file) {
    if ($file == '.' && !empty($_GET['file'])) {
        $line = '<li><a href="/">.</a></li>';
    } elseif ($file == '..' && !empty($_GET['file'])) {
        $line = '<li><a href="?file=' . $lastDir . '">..</a></li>';
    } else {
        if (is_dir($dir . '/' . $file) && $file != '..' && $file != '.') {
            $line = '<li><a href="?file=' . $currentFile . '/' . $file . '"> -- ' . $file . '</a> -
                        <a href="/?delete=' . $currentFile . '/' . $file . '">supprimer</a></li>';
        } else {
            if(!empty($_GET['file']) ){
                $line = '<li><a href="/?open='.$currentFile.'/'.$file.'">' . $file . '</a> - <a href="/?delete=' . $currentFile . '/' . $file . '">supprimer</a></li>';
            }
            else{
                $line='';
            }
        }
    }
    echo $line;
}
echo '</ul>';

include('inc/foot.php');