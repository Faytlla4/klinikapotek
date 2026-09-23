<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
            $title_text = isset($toolbar_title) ? "{$toolbar_title} : " : '';
            if (isset($this->settings_lib)) {
                $title_text .= $this->settings_lib->item('site.title');
            } else {
                $title_text .= 'Klinik & Apotek';
            }
            echo $title_text;
        ?>
    </title>
    <link rel="icon" href="<?php echo base_url(); ?>assets/images/favicon.png" type="image/png" sizes="64x64">
    <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/images/apple-touch-icon.png" sizes="180x180">
    <?php
        Assets::add_css([
            'plugins/fontawesome-free/css/all.min.css',
        ]);
        echo Assets::css();
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/landing.css?v=3" media="screen">
</head>
<body>
    <?php echo theme_view('header'); ?>
    <?php echo Template::message(); ?>
    <?php echo isset($content) ? $content : Template::content(); ?>
    <?php echo theme_view('footer', array('show' => false)); ?>
</body>
</html>
