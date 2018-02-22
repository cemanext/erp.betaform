<?php

$id_del_menu_padre = 0;
$idMenu = "";

if (isset($_GET['idMenu'])) {
    $_SESSION['idMenu'] = $_GET['idMenu'];
    $idMenu = $_GET['idMenu'];

    $sql_padre = "SELECT tipo FROM `lista_menu` WHERE `id` = '".$_GET['idMenu']."' AND livello='".$_SESSION['livello_utente']."'";
    $row_padre = $dblink->get_row($sql_padre, true);
    if(!empty($row_padre)){
        $nome_del_padre = $row_padre['tipo'];
        $sql_id_padre = "SELECT id FROM `lista_menu` WHERE `nome` = '".addslashes($nome_del_padre)."' AND tipo='".CONFIG_TIPO_LISTA_MENU."' AND livello='".$_SESSION['livello_utente']."'";
        $row_id_padre = $dblink->get_row($sql_id_padre, true);
        if(!empty($row_id_padre)){
            $id_del_menu_padre = $row_id_padre['id'];
        }
    }
}
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-105683587-1', 'auto');
  ga('send', 'pageview');

</script>
<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <li class="sidebar-toggler-wrapper hide">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler">
                    <span></span>
                </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>
            <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
            <li class="sidebar-search-wrapper"></li>
                <?php
                $sql = "SELECT * FROM lista_menu WHERE tipo='" . CONFIG_TIPO_LISTA_MENU . "' AND stato='Attivo' $where_lista_menu ORDER BY ordine ASC";
                $row = $dblink->get_row($sql,true);
                if (!empty($row)) {
                    $sql_sub = "SELECT * FROM lista_menu WHERE tipo='" . $row['nome'] . "' AND stato='Attivo' $where_lista_menu ORDER BY ordine ASC";
                    $totale_sub_menu = $dblink->num_rows($sql_sub);
                    $rs = $dblink->get_results($sql);
                    
                    foreach ($rs as $row) {
                        if($row['id']==$id_del_menu_padre){
                            $aprire_il_padre = ' open ';
                            $display = 'style="display: block;"';
                            $padre_selezionato = '<span class="selected"></span>';
                        }else{
                            $aprire_il_padre ='';
                            $display = '';
                            $padre_selezionato = '';
                        }
                        echo '<li class="nav-item  '.$aprire_il_padre.'">';
                        if (strlen(strpos($row['link'], '?')) > 0) {
                            echo '<a href="' . BASE_URL . $row['link'] . '&idMenu=' . $row['id'] . '" class="nav-link nav-toggle">';
                        } else {
                            echo '<a href="' . BASE_URL . $row['link'] . '?idMenu=' . $row['id'] . '" class="nav-link nav-toggle">';
                        }
                        echo '<i class="' . $row['immagine'] . '"></i>
                            <span class="title">' . $row['nome'] . '</span>'.$padre_selezionato.'';
                        $sql_sub = "SELECT * FROM lista_menu WHERE tipo='" . $row['nome'] . "'
                        AND stato='Attivo' $where_lista_menu ORDER BY ordine ASC";
                        $rs_sub = $dblink->get_results($sql_sub);
                        if(!empty($rs_sub)) {
                            $totale_sub_menu = $dblink->num_rows($sql_sub);
                            if ($totale_sub_menu >= 1) {
                                echo '<span class="arrow '.$aprire_il_padre.'"></span>';
                                echo '<ul class="sub-menu" '.$display.'>';
                                
                                foreach ($rs_sub as $row_sub) {
                                if($row_sub['id']==$idMenu){
                                    $sub_active = ' active ';
                                    $selezionato = ' ---> ';
                                }else{
                                    $sub_active = '';
                                    $selezionato = '';
                                }

                                echo' <li class="nav-item '.$sub_active.'">';
                                if (strlen(strpos($row_sub['link'], '?')) > 0) {
                                    echo '<a href="' . BASE_URL . $row_sub['link'] . '&idMenu=' . $row_sub['id'] . '" class="nav-link ">';
                                } else {
                                    echo '<a href="' . BASE_URL . $row_sub['link'] . '?idMenu=' . $row_sub['id'] . '" class="nav-link ">';
                                }
                                echo '<i class="' . $row_sub['immagine'] . '"></i>
                                    <span class="title">' . $row_sub['nome'] . '</span>
                                    </a>
                                    </li>';
                                }
                                echo '</ul>';
                            }
                        }
                        echo '</a>
                            </li>';
                    }
                }
                ?>
            <!-- END SIDEBAR MENU -->
            <!-- END SIDEBAR MENU -->
        </ul>
    </div>
</div>
