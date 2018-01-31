<?php  if(strtolower($_SESSION['livello_utente'])=='amministratore'){ ?>
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
                  <span class="badge badge-success"><?php echo $conteggio_utenti_online;?></span>
                </a>
            </li>
            <li>
                <a href="javascript:;" data-target="#quick_sidebar_tab_2" data-toggle="tab"> Tickets
                    <span class="badge badge-danger">22</span>
                </a>
            </li>
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
                                      <span class="badge badge-success">1</span>
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
                <div class="page-quick-sidebar-item">
                    <div class="page-quick-sidebar-chat-user">
                        <div class="page-quick-sidebar-nav">
                            <a href="javascript:;" class="page-quick-sidebar-back-to-list">
                                <i class="icon-arrow-left"></i>Back</a>
                        </div>
                        <div class="page-quick-sidebar-chat-user-messages">
                            <div class="post out">
                                <img class="avatar" alt="" src="../assets/layouts/layout/img/avatar3.jpg" />
                                <div class="message">
                                    <span class="arrow"></span>
                                    <a href="javascript:;" class="name">Simone Crocco</a>
                                    <span class="datetime">20:15</span>
                                    <span class="body"> Modifica apportata </span>
                                </div>
                            </div>
                            <div class="post in">
                                <img class="avatar" alt="" src="../assets/layouts/layout/img/avatar2.jpg" />
                                <div class="message">
                                    <span class="arrow"></span>
                                    <a href="javascript:;" class="name">Valentina Cucchi</a>
                                    <span class="datetime">20:15</span>
                                    <span class="body"> Ok Grazie </span>
                                </div>
                            </div>
                        </div>
                        <div class="page-quick-sidebar-chat-user-form">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Scrici Messaggio...">
                                <div class="input-group-btn">
                                    <button type="button" class="btn green">
                                        <i class="icon-paper-clip"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php  }else{  } ?>
