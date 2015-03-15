<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?= $this->e($title) ?> - Rest Proxifier</title>

    <!-- Bootstrap core CSS -->
    <link href="<?= $this->asset('css/bootstrap.min.css'); ?>" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?= $this->asset('css/bootstrap-theme.min.css'); ?>" rel="stylesheet">
    <link href="<?= $this->asset('css/style.css'); ?>" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $this->route('index'); ?>">Rest Proxifier</a>
        </div>

    </div>
</nav>


<div class="container">
    <div class="container">
        <div class="starter-template">
            <?= $this->section('content') ?>
        </div>
    </div>

    <hr>

    <footer>
        <p>&copy; ArthurH 2015</p>
    </footer>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="<?= $this->asset('js/bootstrap.min.js'); ?>"></script>
<script src="<?= $this->asset('js/main.js'); ?>"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
</body>
</html>


