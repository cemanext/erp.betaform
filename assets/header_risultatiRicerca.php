<?php

$sql_1 = "SELECT COUNT(*) AS conto FROM `calendario` WHERE LCASE(`stato`) LIKE 'nuovo nominativo in attesa di controllo' $where_calendario";
$row_1 = $dblink->get_row($sql_1, true);
$conteggio_richieste_nuovo_nominativo_in_attesa_di_controllo = $row_1['conto'];

$sql_2 = "SELECT COUNT(*) AS conto FROM `calendario` WHERE LCASE(`stato`) LIKE 'in attesa di controllo' $where_calendario";
$row_2 = $dblink->get_row($sql_2, true);
$conteggio_richieste_in_attesa_di_controllo = $row_2['conto'];

$sql_3 = "SELECT COUNT(*) AS conto FROM `calendario` WHERE LCASE(`stato`) LIKE 'mai contattato' AND id_agente!='7' $where_calendario";
$row_3 = $dblink->get_row($sql_3, true);
$conteggio_richieste_mai_contattato = $row_3['conto'];

$sql_4 = "SELECT COUNT(*) AS conto FROM `calendario` WHERE LCASE(`stato`) LIKE 'richiamare' AND id_agente!='7' $where_calendario";
$row_4 = $dblink->get_row($sql_4, true);
$conteggio_richieste_richiamare = $row_4['conto'];

$sql_5 = "SELECT COUNT(*) AS conto FROM `lista_fatture` WHERE LCASE(`stato`) LIKE 'In Attesa' AND id_agente!='7' $where_lista_fatture";
$row_5 = $dblink->get_row($sql_5, true);
$conteggio_fatture_in_attesa_di_pagamento = $row_5['conto'];

$sql_6 = "SELECT COUNT(*) AS conto FROM `lista_fatture` WHERE LCASE(`stato`) LIKE 'In Attesa di Emissione' AND id_agente!='7' $where_lista_fatture";
$row_6 = $dblink->get_row($sql_6, true);
$conteggio_fatture_in_attesa_di_emissione = $row_6['conto'];

$sql_7 = "SELECT COUNT(*) AS conto FROM `lista_preventivi` WHERE LCASE(`stato`) LIKE 'In Attesa' AND id_agente!='7' $where_lista_preventivi";
$row_7 = $dblink->get_row($sql_7, true);
$conteggio_preventivi_in_attesa = $row_7['conto'];

$sql_8 = "SELECT COUNT(*) AS conto FROM `lista_preventivi` WHERE LCASE(`stato`) LIKE 'Venduto' AND id_agente!='7' $where_lista_preventivi";
$row_8 = $dblink->get_row($sql_8, true);
$conteggio_preventivi_venuti = $row_8['conto'];

$sql_9 = "SELECT COUNT(*) AS conto FROM `lista_ticket` WHERE LCASE(`stato`) LIKE 'In Attesa' $where_lista_ticket";
$row_9 = $dblink->get_row($sql_9, true);
$conteggio_ticket_in_attesa = $row_9['conto'];

$sql_10 = "SELECT COUNT(*) AS conto FROM `lista_ticket` WHERE LCASE(`stato`) LIKE 'In Lavorazione' $where_lista_ticket";
$row_10 = $dblink->get_row($sql_10, true);
$conteggio_ticket_in_lavorazione = $row_10['conto'];

$sql_11 = "SELECT COUNT(*) AS conto FROM `lista_ticket` WHERE LCASE(`stato`) LIKE 'Lavorazione Terminata' AND dataagg BETWEEN DATE_SUB(NOW(), INTERVAL 2 DAY) AND NOW() $where_lista_ticket";
$row_11 = $dblink->get_row($sql_11, true);
$conteggio_ticket_lavorazione_terminata = $row_11['conto'];


?>
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="home.php">
                <img src="<?= BASE_URL ?>/assets/pages/img/logo-mono.png" style="max-height:25px; margin-top:12px;" alt="logo" class="logo-default" /> </a>
            <div class="menu-toggler sidebar-toggler">
                <span></span>
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN HEADER SEARCH BOX -->
        <!-- END HEADER SEARCH BOX -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <?php
                if (($conteggio_richieste_in_attesa_di_controllo+$conteggio_richieste_nuovo_nominativo_in_attesa_di_controllo+$conteggio_richieste_mai_contattato+$conteggio_richieste_richiamare) > 0) {
                    ?>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-hourglass-half"></i>
                            <span class="badge badge-danger"> <?php echo $conteggio_richieste_in_attesa_di_controllo+$conteggio_richieste_nuovo_nominativo_in_attesa_di_controllo+$conteggio_richieste_mai_contattato+$conteggio_richieste_richiamare; ?> </span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if($conteggio_richieste_in_attesa_di_controllo>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_richieste_in_attesa_di_controllo; ?> Richieste </span> In Attesa di Controllo </h3>
                                <a href="<?= BASE_URL ?>/moduli/calendario/index.php?whrStato=a7d7ab5bee5f267d23e0ff28a162bafb"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } if($conteggio_richieste_nuovo_nominativo_in_attesa_di_controllo>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_richieste_nuovo_nominativo_in_attesa_di_controllo; ?> Richieste </span> Nuovo Nominativo In Attesa di Controllo </h3>
                                <a href="<?= BASE_URL ?>/moduli/calendario/index.php?whrStato=6ea4631054884fdcab602cd3dc8cbd7b"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } if($conteggio_richieste_mai_contattato>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_richieste_mai_contattato; ?> Richieste </span> Mai Contattato </h3>
                                <a href="<?= BASE_URL ?>/moduli/calendario/index.php?whrStato=06d52cf8c82bfe8dfbd65f11c1fbca35"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } if($conteggio_richieste_richiamare>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_richieste_richiamare; ?> Richieste </span> Richiamare </h3>
                                <a href="<?= BASE_URL ?>/moduli/calendario/index.php?whrStato=ed59fefc520e30eacbb5fd110761555b"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>

                <?php
                if(($conteggio_preventivi_in_attesa+$conteggio_preventivi_venuti > 0) && $_SESSION['livello_utente']!='commerciale') {
                    ?>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-pencil-square-o"></i>
                            <span class="badge badge-danger"> <?php echo $conteggio_preventivi_in_attesa+$conteggio_preventivi_venuti; ?> </span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if($conteggio_preventivi_venuti>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_preventivi_venuti; ?> Preventivi </span> Venduti </h3>
                                <a href="<?= BASE_URL ?>/moduli/preventivi/index.php?tbl=lista_preventivi&whr_state=0dcf93d17feb1a4f6efe62d5d2f270b2"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                            <?php if($conteggio_preventivi_in_attesa>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_preventivi_in_attesa; ?> Preventivi </span> In Attesa </h3>
                                <a href="<?= BASE_URL ?>/moduli/preventivi/index.php?tbl=lista_preventivi&whr_state=8b7bbcc20d4857c20045195274f7d0dc"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>

                <?php
                if (($conteggio_fatture_in_attesa_di_pagamento > 0 || $conteggio_fatture_in_attesa_di_emissione > 0) && $_SESSION['livello_utente']!='commerciale') {
                    ?>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-money"></i>
                            <span class="badge badge-danger"> <?php echo $conteggio_fatture_in_attesa_di_pagamento+$conteggio_fatture_in_attesa_di_emissione; ?> </span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if($conteggio_fatture_in_attesa_di_pagamento>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_fatture_in_attesa_di_pagamento; ?> Fatture </span> In Attesa di Pagamento </h3>
                                <a href="<?= BASE_URL ?>/moduli/fatture/index.php?tbl=lista_fatture&whr_state=8b7bbcc20d4857c20045195274f7d0dc"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                            <?php if($conteggio_fatture_in_attesa_di_emissione>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_fatture_in_attesa_di_emissione; ?> Fatture </span> In Attesa di Emissione </h3>
                                <a href="<?= BASE_URL ?>/moduli/fatture/index.php?tbl=lista_fatture&whr_state=0c5d1191eb5033b241de0c655ceac356"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
                <!-- END NOTIFICATION DROPDOWN -->
                <!-- BEGIN INBOX DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

                <!-- END INBOX DROPDOWN -->
                <!-- BEGIN TODO DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

                <!-- END TODO DROPDOWN -->
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <?php
                if(strtolower($_SESSION['livello_utente'])=='commerciale'){
                    $reportProduzione = BASE_URL."/moduli/commerciali/homeCommerciale.php?idMenu=34";
                }else{
                    $reportProduzione = BASE_URL."/moduli/commerciali/home.php?idMenu=64";
                }
                ?>
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="<?= BASE_URL ?>/media/users/<?php echo $_SESSION['avatar']; ?>" width="29px" height="29px" />
                        <span class="username username-hide-on-mobile"><?php echo $_SESSION['cognome_nome_utente']; ?></span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <!--<li>
                            <a href="<?=$reportProduzione?>">
                                <i class="icon-speedometer"></i> Report Produzione </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-user"></i> Profilo </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-calendar"></i> Calendario </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-envelope-open"></i> Inbox
                                <span class="badge badge-danger"> 3 </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon-rocket"></i> Tasks
                                <span class="badge badge-success"> 7 </span>
                            </a>
                        </li>-->
                        <li class="divider"> </li>
                        <!--<li>
                            <a href="page_user_lock_1.html">
                                <i class="icon-lock"></i> Lock Screen </a>
                        </li>-->
                        <li>
                            <a href="<?= BASE_URL ?>/logout.php">
                                <i class="icon-key"></i> Log Out </a>
                        </li>
                    </ul>                        </li>
                <!-- END USER LOGIN DROPDOWN -->
                
                <?php
                if(($conteggio_ticket_in_attesa > 0 || $conteggio_ticket_in_lavorazione > 0 || $conteggio_ticket_lavorazione_terminata)) {
                    ?>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-ticket"></i>
                            <span class="badge badge-danger"> <?php echo $conteggio_ticket_in_attesa+$conteggio_ticket_in_lavorazione+$conteggio_ticket_lavorazione_terminata; ?> </span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if($conteggio_ticket_in_attesa>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_ticket_in_attesa; ?> Ticket </span> In Attesa </h3>
                                <a href="<?= BASE_URL ?>/moduli/ticket/index.php?tbl=lista_ticket"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                            <?php if($conteggio_ticket_in_lavorazione>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_ticket_in_lavorazione; ?> Ticket </span> In Lavorazione </h3>
                                <a href="<?= BASE_URL ?>/moduli/ticket/index.php?tbl=lista_ticket"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                            <?php if($conteggio_ticket_lavorazione_terminata>0){ ?>
                            <li class="external">
                                <h3><span class="bold"><?php echo $conteggio_ticket_lavorazione_terminata; ?> Ticket </span> Terminati (ultimi 2 giorni) </h3>
                                <a href="<?= BASE_URL ?>/moduli/ticket/index.php?tbl=lista_ticket"><i class="fa fa-external-link-square"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
                
                <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <?php  if(strtolower($_SESSION['livello_utente'])=='amministratore'){ ?>
                  <li class="dropdown dropdown-quick-sidebar-toggler">
                                  <a href="javascript:;" class="dropdown-toggle">
                                      <i class="icon-logout"></i>
                                  </a>
                              </li>
                <a href="javascript:;" class="page-quick-sidebar-toggler">
                    <i class="icon-login"></i>
                </a>
                <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
                    <div class="page-quick-sidebar">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab"> Utenti Online
                                  <?php
                                  $conteggio_utenti_online = $dblink->num_rows("SELECT * FROM lista_password WHERE livello != 'cliente' AND data_ultimo_accesso > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
                                  ?>
                                  <span class="badge badge-info"><?php echo $conteggio_utenti_online;?></span>
                                </a>
                            </li>
                            <!--<li>
                                <a href="javascript:;" data-target="#quick_sidebar_tab_2" data-toggle="tab"> Tickets
                                    <span class="badge badge-danger">&nbsp;</span>
                                </a>
                            </li>-->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                                <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd" data-wrapper-class="page-quick-sidebar-list">

                                      <!--<h3 class="list-heading">Amministratori</h3>-->
                                      <ul class="media-list list-items">
                                          <?php
                                          $utentiOnline = $dblink->get_results("SELECT * FROM lista_password WHERE livello != 'cliente' AND data_ultimo_accesso > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");

                                          foreach($utentiOnline as $utente){
                                              ?>
                                              <li class="media">
                                                  <div class="media-status">
                                                      <span class="badge badge-success">&nbsp;</span>
                                                  </div>
                                                  <img class="media-object" src="<?= BASE_URL ?>/media/users/<?php echo $utente['avatar']; ?>" alt="...">
                                                  <div class="media-body">
                                                      <h4 class="media-heading"><?php echo $utente['cognome']." ".$utente['nome']; ?></h4>
                                                      <div class="media-heading-sub"> <?php echo $utente['livello'] ?> </div>
                                                    <!--  <div class="media-heading-small"> Last seen 03:10 AM </div>-->
                                                  </div>
                                              </li>
                                              <?php
                                          }

                                          ?>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php  }else{  } ?>
                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
