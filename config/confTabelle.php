<?php
//include_once(BASE_ROOT . 'config/confAccesso.php');
//include_once(BASE_ROOT . 'config/confDebug.php');

/** TABELLA lista_telefonate  **/
$table_listaTelefonate = array( //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_telefonate&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
//                              //CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_telefonate&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                               //CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_telefonate&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'
                "index" => array("campi" => "
                                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_telefonate&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                stato, dataagg, (SELECT CONCAT(cognome,' ', nome) AS nome FROM lista_password WHERE livello NOT LIKE 'cliente' AND id = id_password LIMIT 1) AS commerciale, codice, codice_interno, codice_esterno, 
                                data_chiamata_inizio, data_chiamata_fine, durata_chiamata, mittente,
                                destinatario, campo_1, note",
                                "where" => "1 ".$where_lista_telefonate,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true
                ),
                array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true
                ),
                array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true
                ),
                array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("TerminatedByDst"=>"TerminatedByDst", "Non Attivo"=>"Non Attivo")
                ),
                array(  "campo" => "id_password",
                        "tipo" => "select2",
                        "etichetta" => "Commerciale",
                        "readonly" => true,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_password WHERE livello NOT LIKE 'cliente' ORDER BY nome ASC"
                ),
                array(
                "campo" => "mittente",
                "tipo" => "input",
                "etichetta" => "Mittente",
                "readonly" => false
                ),
                array(
                "campo" => "destinatario",
                "tipo" => "input",
                "etichetta" => "Destinatario",
                "readonly" => false
                ),
                array(
                "campo" => "note",
                "tipo" => "text",
                "etichetta" => "Note",
                "readonly" => false
                ))
        );

/** TABELLA lista_obiezioni  **/
$table_listaObiezioni = array( //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_obiezioni&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                               //CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_richieste_stati&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'
                "index" => array("campi" => "
                                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_obiezioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_obiezioni&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                nome as 'Nome', descrizione as 'Descrizione', stato as 'Stato'",
                                "where" => "1 ".$where_lista_obiezioni,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true
                ),
                array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true
                ),
                array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true
                ),
                array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                ),
                array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false
                ),
                array(
                "campo" => "descrizione",
                "tipo" => "input",
                "etichetta" => "Descrizione",
                "readonly" => false
                ))
        );

/** TABELLA lista_esami_corsi_commerciali  **/
$table_listaEsamiCorsiCommerciali = array( //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/corsi/dettaglio.php?tbl=calendario_esami&id=',id,'&idProdotto=',id_prodotto,'',IF(etichetta LIKE 'Calendario Esami','&esame=1','&esame=0'),'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                "index" => array("campi" => " ora, data,
                                            IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo',
                                            CONCAT('<B>',oggetto,'</B>') AS Oggetto, 
                                            IF(id_aula>0, (SELECT nome FROM lista_aule WHERE id = id_aula),'') AS 'Aula',
                                            (SELECT COUNT(*) FROM matrice_corsi_docenti WHERE matrice_corsi_docenti.id_calendario = calendario.id AND calendario.id_prodotto = matrice_corsi_docenti.id_prodotto) AS 'N. Docente',
                                            
                                            CONCAT('Aula: ',numerico_4,'<br>Docenti: ',numerico_5,'<br>Extra: ',numerico_3) AS 'Costi', 
                                            campo_13 AS 'Durata Corso',
                                            numerico_10 AS 'Iscritti', stato",
                                            //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                "where" => " 1 AND (etichetta LIKE 'Calendario Esami' OR etichetta LIKE 'Calendario Corsi') ".$where_calendario_esami,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "etichetta",
                        "tipo" => "select_static",
                        "etichetta" => "Etichetta",
                        "readonly" => false,
                        "sql" => array("Calendario Esami"=>"Calendario Esami", "Calendario Corsi"=>"Calendario Corsi")
                    ),
                    array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Corso",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_prodotti WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                array(  "campo" => "data",
                        "tipo" => "data",
                        "etichetta" => "Data",
                        "readonly" => false
                    ),
                array(  "campo" => "ora",
                        "tipo" => "ora",
                        "etichetta" => "Ora",
                        "readonly" => false
                    ),
                array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => false
                    ),
                array(  "campo" => "ora_fine",
                        "tipo" => "ora",
                        "etichetta" => "Ora Fine",
                        "readonly" => false
                    ),
                array(  "campo" => "oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto",
                        "readonly" => false
                    ),
                array(  "campo" => "messaggio",
                        "tipo" => "text",
                        "etichetta" => "Messaggio",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_aula",
                        "tipo" => "select2",
                        "etichetta" => "Aula",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_aule WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                     array(  "campo" => "numerico_3",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Extra",
                        "readonly" => false
                    ),
                    array(  "campo" => "numerico_4",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Aula",
                        "readonly" => false
                    ),
                    array(  "campo" => "numerico_5",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Docenti",
                        "readonly" => false
                    ),
                    array(  "campo" => "campo_13",
                        "tipo" => "numerico",
                        "etichetta" => "Durata Corso",
                        "readonly" => false
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
            );


/** TABELLA LISTA_AZIENDE **/
$table_listaAziende = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_aziende&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_aziende&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_aziende&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
                                            CONCAT('<b>',`ragione_sociale`,'</b>') AS 'Azienda', codice, partita_iva, codice_fiscale, telefono, stato",
                                "where" => " 1 ".$where_lista_aziende,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),
                array(  "campo" => "codice_esterno",
                        "tipo" => "input",
                        "etichetta" => "Codice Est.",
                        "readonly" => true
                    ),
                array(  "campo" => "ragione_sociale",
                        "tipo" => "input",
                        "etichetta" => "Rag. Sociale",
                        "readonly" => false
                    ),
                array(  "campo" => "forma_giuridica",
                        "tipo" => "select_static",
                        "etichetta" => "Forma Giuridica",
                        "readonly" => false,
                        "sql" => array("SS"=>"SS", "SNC"=>"SNC", "SAS"=>"SAS", "SRL"=>"SRL", "SPA"=>"SPA", "SAPA"=> "SAPA", "Soc. Coop."=>"Soc. Coop.", "Ditta Individuale"=>"Ditta Individuale", "Libero Professionista"=>"Libero Professionista")
                    ),
                array(  "campo" => "partita_iva",
                        "tipo" => "input",
                        "etichetta" => "P.Iva",
                        "readonly" => false
                    ),
                array(  "campo" => "codice_fiscale",
                        "tipo" => "input",
                        "etichetta" => "Codice Fiscale",
                        "readonly" => false
                    ),
                array(  "campo" => "indirizzo",
                        "tipo" => "indirizzo",
                        "etichetta" => "Indirizzo",
                        "readonly" => false
                    ),
                array(  "campo" => "cap",
                        "tipo" => "cap",
                        "etichetta" => "CAP",
                        "readonly" => false
                    ),
                array(  "campo" => "citta",
                        "tipo" => "input",
                        "etichetta" => "Città",
                        "readonly" => false
                    ),
                array(  "campo" => "provincia",
                        "tipo" => "input",
                        "etichetta" => "Prov.",
                        "readonly" => false
                    ),
                array(  "campo" => "nazione",
                        "tipo" => "input",
                        "etichetta" => "Nazione",
                        "readonly" => false
                    ),
                array(  "campo" => "telefono",
                        "tipo" => "telefono",
                        "etichetta" => "Telefono",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "fax",
                        "tipo" => "fax",
                        "etichetta" => "Fax",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "web",
                        "tipo" => "web",
                        "etichetta" => "Sito Web",
                        "readonly" => false
                    ),
                array(  "campo" => "categoria",
                        "tipo" => "input",
                        "etichetta" => "Categoria",
                        "readonly" => false
                    ),
                array(  "campo" => "settore",
                        "tipo" => "input",
                        "etichetta" => "Settore",
                        "readonly" => false
                    ),
                array(  "campo" => "tipo",
                        "tipo" => "select2",
                        "etichetta" => "Tipo",
                        "readonly" => false,
                        "sql" => "SELECT id as valore, nome FROM lista_menu WHERE stato='Attivo'"
                    ),
                array(  "campo" => "note",
                        "tipo" => "text",
                        "etichetta" => "Note",
                        "readonly" => false
                    ),
                array(  "campo" => "id_agente",
                        "tipo" => "select2",
                        "etichetta" => "Commerciale",
                        "readonly" => false,
                        "sql" => "SELECT id as valore, CONCAT(cognome,' ', nome) as nome FROM lista_password WHERE stato='Attivo' AND livello='commerciale' ORDER BY cognome, nome ASC"
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
            );

/** TABELLA LISTA_PROFESSIONISTI **/
$table_listaProfessionisti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_professionisti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_professionisti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"dettaglio_tab.php?tbl=lista_professionisti&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            CONCAT('<b>',`cognome`,' ',`nome`,'</b>') AS 'Professionista', codice_fiscale, cellulare, telefono, email, stato",
                                "where" => " 1 ".$where_lista_professionisti,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "data_creazione",
                        "tipo" => "hidden",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),
                array(  "campo" => "titolo",
                        "tipo" => "select-cancella",
                        "etichetta" => "Titolo",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM `lista_titoli` WHERE `stato` LIKE 'Attivo' ORDER BY `nome` ASC"
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "cognome",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "data_di_nascita",
                        "tipo" => "data",
                        "etichetta" => "Data di nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "luogo_di_nascita",
                        "tipo" => "input",
                        "etichetta" => "Luogo di nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "provincia_di_nascita",
                        "tipo" => "input",
                        "etichetta" => "Provincia di Nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "codice_fiscale",
                        "tipo" => "codice_fiscale",
                        "etichetta" => "Codice Fiscale",
                        "readonly" => false
                    ),
                /*array(  "campo" => "professione",
                        "tipo" => "input",
                        "etichetta" => "Professione",
                        "readonly" => false
                    ),*/
                array(  "campo" => "professione",
                        "tipo" => "select2",
                        "etichetta" => "Professione",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM  `lista_professioni` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    ),
                    array(
                        "campo" => "attestato_classe",
                        "tipo" => "select_static",
                        "etichetta" => "Usa Classe per Attestato",
                        "readonly" => false,
                        "sql" => array("Si"=>"Si", "No"=>"No")
                    ),
                array(  "campo" => "id_classe",
                        "tipo" => "select2",
                        "etichetta" => "Tipo Albo",
                        "readonly" => false,
                        "sql" => "SELECT id as valore, nome AS nome FROM lista_classi ORDER BY nome"
                    ),
                array(  "campo" => "provincia_albo",
                        "tipo" => "input",
                        "etichetta" => "Provincia Albo",
                        "readonly" => false
                    ),
                array(  "campo" => "numero_albo",
                        "tipo" => "numerico",
                        "etichetta" => "Num. Albo",
                        "readonly" => false
                    ),
                array(  "campo" => "telefono",
                        "tipo" => "telefono",
                        "etichetta" => "Telefono",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "fax",
                        "tipo" => "fax",
                        "etichetta" => "Fax",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "web",
                        "tipo" => "web",
                        "etichetta" => "Sito Web",
                        "readonly" => false
                    ),
                array(  "campo" => "note",
                        "tipo" => "text",
                        "etichetta" => "Note",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_1",
                        "tipo" => "hidden",
                        "etichetta" => "Campo 1",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_2",
                        "tipo" => "hidden",
                        "etichetta" => "Campo 2",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_3",
                        "tipo" => "hidden",
                        "etichetta" => "Campo 3",
                        "readonly" => false,
                        "default" => " ",
                        "forza_valore_default" => true
                    ),
                array(  "campo" => "numerico_1",
                        "tipo" => "hidden",
                        "etichetta" => "Numerico 1",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_2",
                        "tipo" => "hidden",
                        "etichetta" => "Numerico 2",
                        "readonly" => false
                    ),
                array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "In Attesa di Eliminazione"=>"In Attesa di Eliminazione")
                    )),
                "esporta" => array(
                    array(  "campo" => "codice",
                            "tipo" => "input",
                            "etichetta" => "Codice Cliente",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "nome",
                            "tipo" => "input",
                            "etichetta" => "Nome",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => true,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "cognome",
                            "tipo" => "input",
                            "etichetta" => "Cognome",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "data_di_nascita",
                            "tipo" => "confronto_data",
                            "etichetta" => "Data di Nascita",
                            "readonly" => true,
                            "like" => false,
                            "uguale" => false,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "luogo_di_nascita",
                            "tipo" => "input",
                            "etichetta" => "Luogo di Nascita",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "provincia_di_nascita",
                            "tipo" => "select2",
                            "etichetta" => "Provincia di Nascita",
                            "readonly" => false,
                            "like" => false,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false,
                            "sql" => "SELECT sigla_province as valore, sigla_province AS nome FROM lista_province ORDER BY sigla_province ASC"
                        ),
                    array(  "campo" => "codice_fiscale",
                            "tipo" => "input",
                            "etichetta" => "Codice Fiscale",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "professione",
                            "tipo" => "input",
                            "etichetta" => "Professione",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "id_classe",
                            "tipo" => "select2",
                            "etichetta" => "Tipo Albo",
                            "readonly" => false,
                            "like" => false,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false,
                            "sql" => "SELECT id as valore, nome AS nome FROM lista_classi WHERE 1 ORDER BY nome ASC"
                        ),
                    array(  "campo" => "provincia_albo",
                            "tipo" => "select2",
                            "etichetta" => "Provincia Albo",
                            "readonly" => false,
                            "like" => false,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false,
                            "sql" => "SELECT sigla_province as valore, sigla_province AS nome FROM lista_province ORDER BY sigla_province ASC"
                        ),
                    array(  "campo" => "numero_albo",
                            "tipo" => "input",
                            "etichetta" => "Numero Albo",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "telefono",
                            "tipo" => "input",
                            "etichetta" => "Telefono",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "cellulare",
                            "tipo" => "input",
                            "etichetta" => "Cellulare",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "fax",
                            "tipo" => "input",
                            "etichetta" => "Fax",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "email",
                            "tipo" => "input",
                            "etichetta" => "E-Mail",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "web",
                            "tipo" => "input",
                            "etichetta" => "Sito Web",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                    array(  "campo" => "note",
                            "tipo" => "input",
                            "etichetta" => "Note",
                            "readonly" => false,
                            "like" => true,
                            "uguale" => true,
                            "maggiore" => false,
                            "default" => "",
                            "attivo" => false
                        ),
                )
            );


/** TABELLA LISTA_PROFESSIONI **/
$table_listaProfessioni = array(
                "index" => array("campi" => "nome AS 'Professione', codice AS 'Codice Professione', stato AS 'Stato',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_professioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_professioni&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => " 1 ".$where_lista_professioni,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                    array(
                    "campo" => "id",
                    "tipo" => "hidden",
                    "etichetta" => "Id",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "dataagg",
                    "tipo" => "hidden",
                    "etichetta" => "Dataagg",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "scrittore",
                    "tipo" => "hidden",
                    "etichetta" => "Scrittore",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "nome",
                    "tipo" => "input",
                    "etichetta" => "Nome",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "codice",
                    "tipo" => "input",
                    "etichetta" => "Codice",
                    "readonly" => false
                    ),
                    array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
                );

/** TABELLA LISTA_PROFESSIONI **/
$table_listaAlbiProfessionali = array(
                "index" => array("campi" => "nome AS 'Nome Albo', codice AS 'Codice Albo', stato AS 'Stato',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_albi_professionali&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_albi_professionali&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => " 1 ".$where_lista_albi_professionali,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                    array(
                    "campo" => "id",
                    "tipo" => "hidden",
                    "etichetta" => "Id",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "dataagg",
                    "tipo" => "hidden",
                    "etichetta" => "Dataagg",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "scrittore",
                    "tipo" => "hidden",
                    "etichetta" => "Scrittore",
                    "readonly" => true
                    ),
                    array(
                    "campo" => "nome",
                    "tipo" => "input",
                    "etichetta" => "Nome",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "codice",
                    "tipo" => "input",
                    "etichetta" => "Codice",
                    "readonly" => false
                    ),
                    array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
                );

/** TABELLA LISTA_PASSWORD **/
$table_listaPassword = array( //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_password&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                "index" => array("campi" => "
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_password&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<h4>',`cognome`,' ',`nome`,'</h4><small>(',id_professionista,')</small>') AS 'Professionista',
                                            username AS 'Nome Utente', livello, CONCAT('Creato: <br>',DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y'),'<br>Scadenza: <br>',DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y'),'<br>Ultimo: <br>',DATE_FORMAT(DATE(data_ultimo_accesso), '%d-%m-%Y')) AS 'Validit&agrave;', email AS 'E-Mail', `id_moodle_user` AS 'Id MOODLE', stato AS 'Stato'",
                                "where" => " 1 ".$where_lista_password,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "hidden",
                        "etichetta" => "ID Professionista",
                        "readonly" => true
                    ),
                /*array(  "campo" => "data_creazione",
                        "tipo" => "hidden",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),*/
                    array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "livello",
                        "tipo" => "select2",
                        "etichetta" => "Livello",
                        "readonly" => false,
                        "sql" => "SELECT nome as valore, nome FROM lista_utenti_livelli WHERE stato='Attivo' AND nome!='amministratore'"
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "cognome",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "username",
                        "tipo" => "username",
                        "etichetta" => "Nome Utente",
                        "readonly" => false
                    ),
                array(  "campo" => "passwd",
                        "tipo" => "password",
                        "etichetta" => "Password",
                        "readonly" => false
                    ),
                array(  "campo" => "passwd_email",
                        "tipo" => "password",
                        "etichetta" => "Password E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "nickname",
                        "tipo" => "input",
                        "etichetta" => "Nickname",
                        "readonly" => false
                    ),
                array(  "campo" => "avatar",
                        "tipo" => "file",
                        "etichetta" => "File Avatar",
                        "readonly" => false,
                        "dir" => BASE_ROOT."media/users/"
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ),
                array(  "campo" => "numerico_1",
                        "tipo" => "numerico",
                        "etichetta" => "Numero Interno",
                        "readonly" => false
                    ),
                /*array(  "campo" => "numerico_2",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 2",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_3",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 3",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_4",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 4",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_5",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 5",
                        "readonly" => false
                    ),*/
                array(  "campo" => "id_moodle_user",
                    "tipo" => "numerico",
                    "etichetta" => "ID MOODLE",
                    "readonly" => true
                ),
                array(  "campo" => "firma_email",
                    "tipo" => "htmlarea",
                    "etichetta" => "FIRMA E-MAIL",
                    "readonly" => false
                ))
            );

/** TABELLA LISTA_PASSWORD UTENTI MOODLE **/
$table_listaPasswordUtenti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_password_utenti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<h4>',`cognome`,' ',`nome`,'</h4><small>(',id_professionista,')</small>') AS 'Professionista',
                                            username AS 'Nome Utente', livello, CONCAT('Creato: <br>',DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y'),'<br>Scadenza: <br>',DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y'),'<br>Ultimo: <br>',DATE_FORMAT(DATE(data_ultimo_accesso), '%d-%m-%Y')) AS 'Validit&agrave;', email AS 'E-Mail', `id_moodle_user` AS 'Id MOODLE', stato AS 'Stato'",
                                "where" => " 1 ".$where_lista_password,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "hidden",
                        "etichetta" => "ID Professionista",
                        "readonly" => true
                    ),
                /*array(  "campo" => "data_creazione",
                        "tipo" => "hidden",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),*/
                    array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "livello",
                        "tipo" => "select2",
                        "etichetta" => "Livello",
                        "readonly" => false,
                        "sql" => "SELECT nome as valore, nome FROM lista_utenti_livelli WHERE stato='Attivo' AND nome='cliente'"
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "cognome",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "username",
                        "tipo" => "username",
                        "etichetta" => "Nome Utente",
                        "readonly" => true
                    ),
                array(  "campo" => "passwd",
                        "tipo" => "input",
                        "etichetta" => "Password",
                        "readonly" => true
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => true
                    ),
                array(  "campo" => "nickname",
                        "tipo" => "input",
                        "etichetta" => "Nickname",
                        "readonly" => false
                    ),
                array(  "campo" => "avatar",
                        "tipo" => "file",
                        "etichetta" => "File Avatar",
                        "readonly" => false,
                        "dir" => BASE_ROOT."media/users/"
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ),
                    array(  "campo" => "id_moodle_user",
                        "tipo" => "numerico",
                        "etichetta" => "ID MOODLE",
                        "readonly" => true
                    ))
            );

/*
"index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale',
                                            oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                            //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                "where" => " 1 ".$where_calendario,
                                "order" => "ORDER BY id DESC"),
 */

/** TABELLA CALENDARIO **/
$table_calendario = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale',
                                            stato, IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                                            mittente AS 'Mittente', (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing",
                                            //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                            /*
                                            
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'
                                            */
                                "where" => " 1 ".$where_calendario.$where_calendario_all,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "id_lista_calendari",
                        "tipo" => "hidden",
                        "etichetta" => "ID Lista Calendari",
                        "readonly" => true
                    ),
                array(  "campo" => "id_contatto",
                        "tipo" => "hidden",
                        "etichetta" => "ID Contatto",
                        "readonly" => true
                    ),
                array(  "campo" => "id_preventivo",
                        "tipo" => "hidden",
                        "etichetta" => "ID Preventivo",
                        "readonly" => true
                    ),
                array(  "campo" => "id_commessa",
                        "tipo" => "hidden",
                        "etichetta" => "ID Commessa",
                        "readonly" => true
                    ),
                array(  "campo" => "id_commessa_dettaglio",
                        "tipo" => "hidden",
                        "etichetta" => "ID Commessa Dett.",
                        "readonly" => true
                    ),
                array(  "campo" => "id_fattura",
                        "tipo" => "hidden",
                        "etichetta" => "ID Fattura",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "datainsert",
                        "tipo" => "data",
                        "etichetta" => "Data Inserimento",
                        "readonly" => true
                    ),
                array(  "campo" => "orainsert",
                        "tipo" => "ora",
                        "etichetta" => "Ora Inserimento",
                        "readonly" => true
                    ),
                array(  "campo" => "data",
                        "tipo" => "data",
                        "etichetta" => "Data",
                        "readonly" => true
                    ),
                array(  "campo" => "ora",
                        "tipo" => "ora",
                        "etichetta" => "Ora",
                        "readonly" => true
                    ),
                array(  "campo" => "etichetta",
                        "tipo" => "bs-select",
                        "etichetta" => "Etichetta",
                        "readonly" => false,
                        "sql" => "SELECT tipo AS valore, tipo AS nome, colore_sfondo AS colore FROM tipologie_calendario WHERE 1"
                    ),
                array(  "campo" => "oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto",
                        "readonly" => false
                    ),
                array(  "campo" => "messaggio",
                        "tipo" => "text",
                        "etichetta" => "Messaggio",
                        "readonly" => false
                    ),
                array(  "campo" => "mittente",
                        "tipo" => "input",
                        "etichetta" => "Mittente",
                        "readonly" => false
                    ),
                array(  "campo" => "destinatario",
                        "tipo" => "input",
                        "etichetta" => "Destinario",
                        "readonly" => false
                    ),
                array(  "campo" => "priorita",
                        "tipo" => "select_static",
                        "etichetta" => "Priorità",
                        "readonly" => false,
                        "sql" => array("Urgente"=>"Urgente", "Alta"=>"Alta", "Normale"=>"Normale", "Bassa"=>"Bassa")
                    ),
                array(  "campo" => "stato",
                        "tipo" => "bs-select",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM lista_richieste_stati WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                array(  "campo" => "ricerca",
                        "tipo" => "input",
                        "etichetta" => "Ricerca",
                        "readonly" => false
                    ),
                array(  "campo" => "giorno",
                        "tipo" => "numerico",
                        "etichetta" => "Giorno",
                        "readonly" => false
                    ),
                array(  "campo" => "mese",
                        "tipo" => "numerico",
                        "etichetta" => "Mese",
                        "readonly" => false
                    ),
                array(  "campo" => "anno",
                        "tipo" => "numerico",
                        "etichetta" => "Anno",
                        "readonly" => false
                    ),
                array(  "campo" => "colore_testo",
                        "tipo" => "color_picker",
                        "etichetta" => "Colore Testo",
                        "readonly" => false
                    ),
                array(  "campo" => "colore_sfondo",
                        "tipo" => "color_picker",
                        "etichetta" => "Colore Sfondo",
                        "readonly" => false
                    ),
                array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => true
                    ),
                array(  "campo" => "ora_fine",
                        "tipo" => "ora",
                        "etichetta" => "Ora Fine",
                        "readonly" => true
                    ),
                array(  "campo" => "durata",
                        "tipo" => "numerico",
                        "etichetta" => "Durata",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_1",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_2",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_3",
                        "tipo" => "input",
                        "etichetta" => "Codice Cliente ",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_4",
                        "tipo" => "input",
                        "etichetta" => "Telefono",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_5",
                        "tipo" => "input",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_6",
                        "tipo" => "input",
                        "etichetta" => "Tipo Marketing",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_7",
                        "tipo" => "input",
                        "etichetta" => "Nome Campagna",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_8",
                        "tipo" => "input",
                        "etichetta" => "Url",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_9",
                        "tipo" => "input",
                        "etichetta" => "Campo 9",
                        "readonly" => false
                    ),
                array(  "campo" => "campo_10",
                        "tipo" => "input",
                        "etichetta" => "Campo 10",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_1",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 1",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_2",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 2",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_3",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 3",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_4",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 4",
                        "readonly" => false
                    ),
                array(  "campo" => "numerico_5",
                        "tipo" => "numerico",
                        "etichetta" => "Numerico 5",
                        "readonly" => false
                    ),
                array(  "campo" => "notifica_email",
                        "tipo" => "select_static",
                        "etichetta" => "Notifica E-Mail",
                        "readonly" => false,
                        "sql" => array("Si"=>"Si", "No"=>"No")
                    ),
                array(  "campo" => "notifica_sms",
                        "tipo" => "select_static",
                        "etichetta" => "Notifica SMS",
                        "readonly" => false,
                        "sql" => array("Si"=>"Si", "No"=>"No")
                    )),
            "esporta" => array(
                array(  "campo" => "datainsert",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Inserimento",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                /*array(  "campo" => "orainsert",
                        "tipo" => "ora",
                        "etichetta" => "Ora Inserimento",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                    ),*/
                array(  "campo" => "data",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Richiamo",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                /*array(  "campo" => "ora",
                        "tipo" => "ora",
                        "etichetta" => "Ora Richiamo",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                    ),*/
                array(  "campo" => "etichetta",
                        "tipo" => "input",
                        "etichetta" => "Etichetta",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "Nuova Richiesta",
                        "attivo" => true
                    ),
                array(  "campo" => "oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "messaggio",
                        "tipo" => "text",
                        "etichetta" => "Messaggio",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "mittente",
                        "tipo" => "input",
                        "etichetta" => "Mittente",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "id_agente",
                        "tipo" => "select2",
                        "etichetta" => "Commerciale",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false,
                        "sql" => "SELECT id as valore, CONCAT(lista_password.cognome,' ',lista_password.nome) AS nome FROM lista_password WHERE lista_password.stato='Attivo' AND lista_password.livello='commerciale' ORDER BY lista_password.cognome ASC, lista_password.nome ASC "
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select2",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false,
                        "sql" => "SELECT stato AS valore, stato AS nome FROM calendario WHERE etichetta LIKE 'Nuova Richiesta' AND stato NOT LIKE 'Fatto' GROUP BY stato ORDER BY stato ASC"
                    ),
                array(  "campo" => "campo_1",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "campo_2",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "campo_3",
                        "tipo" => "input",
                        "etichetta" => "Codice Fiscale ",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "campo_4",
                        "tipo" => "input",
                        "etichetta" => "Telefono",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "campo_5",
                        "tipo" => "input",
                        "etichetta" => "E-Mail",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "id_tipo_marketing",
                        "tipo" => "select2",
                        "etichetta" => "Tipo Marketing",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_tipo_marketing WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                array(  "campo" => "id_campagna",
                        "tipo" => "select2",
                        "etichetta" => "Nome Campagna",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_campagne ORDER BY nome ASC"
                    ),
                array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Nome Prodotto",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_prodotti WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                array(  "campo" => "campo_8",
                        "tipo" => "input",
                        "etichetta" => "Url",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ))
            );

/** TABELLA TICKETS **/
//mittente AS 'Mittente', oggetto AS 'Oggetto', messaggio AS 'Messaggio', 
$table_listaTickets = array(
                            "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_ticket&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                                       dataagg, 
                                                        (SELECT CONCAT(`colore_sfondo`,'|', `nome`) FROM lista_ticket_stati WHERE lista_ticket_stati.`nome` LIKE `lista_ticket`.`stato`) AS `stato`,
                                                        CONCAT('<div style=\"text-align:right;\"><small>MIttente: <b>',mittente,'</b> | Priorit&agrave;: <b>',priorita,'</b></small></div>
                                                        <div style=\"text-align:left;\">Oggetto: <b>',oggetto,'</b></div>
                                                        <div style=\"text-align:left; padding:10px;\">',REPLACE(IF(LENGTH(messaggio)>0, messaggio, ''), '\n', '<br />'),'</div>
                                                        <div style=\"text-align:right;\"><small>',dataagg,' | ',stato,'</small></div>') as 'TICKET'
                                                        ",
                                                        //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"rispondiTicket.php?idTicket=',id,'\" data-target=\"#ajax\" data-url=\"rispondiTicket.php?idTicket=',id,'\" data-toggle=\"modal\" title=\"RISPONDI\" alt=\"RISPONDI\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Rispondi',
                                                        //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                                        //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                            "where" => " 1 ".$where_lista_ticket,
                                            "order" => "ORDER BY stato ASC, dataagg ASC"),
                            "modifica" => array(
                            array(  "campo" => "id",
                                    "tipo" => "hidden",
                                    "etichetta" => "ID",
                                    "readonly" => true
                                ),
                            array(  "campo" => "dataagg",
                                    "tipo" => "hidden",
                                    "etichetta" => "Data Agg.",
                                    "readonly" => true
                                ),
                            array(  "campo" => "scrittore",
                                    "tipo" => "input",
                                    "etichetta" => "Scrittore",
                                    "readonly" => true
                                ),
                            array(  "campo" => "datainsert",
                                    "tipo" => "data",
                                    "etichetta" => "Data Inserimento",
                                    "readonly" => true
                                ),
                            array(  "campo" => "orainsert",
                                    "tipo" => "ora",
                                    "etichetta" => "Ora Inserimento",
                                    "readonly" => true
                                ),
                            array(  "campo" => "data",
                                    "tipo" => "data",
                                    "etichetta" => "Data",
                                    "readonly" => true
                                ),
                            array(  "campo" => "ora",
                                    "tipo" => "ora",
                                    "etichetta" => "Ora",
                                    "readonly" => true
                                ),
                            array(  "campo" => "etichetta",
                                    "tipo" => "bs-select",
                                    "etichetta" => "Etichetta",
                                    "readonly" => false,
                                    "sql" => "SELECT tipo AS valore, tipo AS nome, colore_sfondo AS colore FROM tipologie_calendario WHERE 1"
                                ),
                            array(  "campo" => "oggetto",
                                    "tipo" => "input",
                                    "etichetta" => "Oggetto",
                                    "readonly" => false
                                ),
                            array(  "campo" => "messaggio",
                                    "tipo" => "text",
                                    "etichetta" => "Messaggio",
                                    "readonly" => false
                                ),
                            array(  "campo" => "mittente",
                                    "tipo" => "input",
                                    "etichetta" => "Mittente",
                                    "readonly" => false
                                ),
                            array(  "campo" => "destinatario",
                                    "tipo" => "input",
                                    "etichetta" => "Destinario",
                                    "readonly" => false
                                ),
                            array(  "campo" => "priorita",
                                    "tipo" => "select_static",
                                    "etichetta" => "Priorità",
                                    "readonly" => false,
                                    "sql" => array("Urgente"=>"Urgente", "Alta"=>"Alta", "Normale"=>"Normale", "Bassa"=>"Bassa")
                                ),
                            array(  "campo" => "stato",
                                    "tipo" => "bs-select",
                                    "etichetta" => "Stato",
                                    "readonly" => false,
                                    "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM lista_ticket_stati WHERE stato='Attivo' ORDER BY nome ASC"
                                ),
                            array(  "campo" => "data_fine",
                                    "tipo" => "data",
                                    "etichetta" => "Data Fine",
                                    "readonly" => true
                                ),
                            array(  "campo" => "ora_fine",
                                    "tipo" => "ora",
                                    "etichetta" => "Ora Fine",
                                    "readonly" => true
                                ),

                            array(  "campo" => "url",
                                    "tipo" => "input",
                                    "etichetta" => "Url",
                                    "readonly" => false
                                ),
                            array(  "campo" => "allegato",
                                    "tipo" => "input",
                                    "etichetta" => "Allegato",
                                    "readonly" => false
                                ),
                            array(  "campo" => "campo_3",
                                    "tipo" => "input",
                                    "etichetta" => "Campo 3 ",
                                    "readonly" => false
                                ),
                            array(  "campo" => "numerico_1",
                                    "tipo" => "numerico",
                                    "etichetta" => "Numerico 1",
                                    "readonly" => false
                                ),
                            array(  "campo" => "numerico_2",
                                    "tipo" => "numerico",
                                    "etichetta" => "Numerico 2",
                                    "readonly" => false
                                ),
                            array(  "campo" => "notifica_email",
                                    "tipo" => "select_static",
                                    "etichetta" => "Notifica E-Mail",
                                    "readonly" => false,
                                    "sql" => array("Si"=>"Si", "No"=>"No")
                                ),
                            array(  "campo" => "notifica_sms",
                                    "tipo" => "select_static",
                                    "etichetta" => "Notifica SMS",
                                    "readonly" => false,
                                    "sql" => array("Si"=>"Si", "No"=>"No")
                                ))
                        );
                        
/** TABELLA TICKETS **/
$table_listaTicketsDettaglio = array(
                            "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_ticket&id=',id_ticket,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                                        dataagg,
                                                        (SELECT CONCAT(`colore_sfondo`,'|', `nome`) FROM lista_ticket_stati WHERE lista_ticket_stati.`nome` LIKE `lista_ticket`.`stato`) AS `stato`,
                                                        mittente AS 'Mittente', messaggio AS 'Messaggio'",
                                                        //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"rispondiTicket.php?idTicket=',id,'\" data-target=\"#ajax\" data-url=\"rispondiTicket.php?idTicket=',id,'\" data-toggle=\"modal\" title=\"RISPONDI\" alt=\"RISPONDI\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Rispondi',
                                                        //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                                        //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                            "where" => " 1 ".$where_lista_ticket,
                                            "order" => "ORDER BY stato ASC, dataagg ASC"),
                            "modifica" => array(
                            array(  "campo" => "id",
                                    "tipo" => "hidden",
                                    "etichetta" => "ID",
                                    "readonly" => true
                                ),
                                array(  "campo" => "id_ticket",
                                    "tipo" => "hidden",
                                    "etichetta" => "Id Ticket",
                                    "readonly" => true
                                ),
                            array(  "campo" => "dataagg",
                                    "tipo" => "hidden",
                                    "etichetta" => "Data Agg.",
                                    "readonly" => true
                                ),
                            array(  "campo" => "scrittore",
                                    "tipo" => "input",
                                    "etichetta" => "Scrittore",
                                    "readonly" => true
                                ),
                            array(  "campo" => "messaggio",
                                    "tipo" => "text",
                                    "etichetta" => "Messaggio",
                                    "readonly" => false
                                ))
                        );

/** TABELLA LISTA_TICKET_STATI **/
$table_listaTicketStati = array(
                            "index" => array("campi" => "
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_ticket_stati&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_ticket_stati&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_ticket_stati&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                            CONCAT(`colore_sfondo`,'|', `nome`) AS 'Nome', descrizione as 'Descrizione', 
                                            `stato`,
                                            livello AS 'Livello', colore_sfondo as 'Colore',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_ticket_stati&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                            "where" => "1 ".$where_lista_ticket_stati,
                                            "order" => "ORDER BY id DESC"),
                            "modifica" => array(
                            array(
                            "campo" => "id",
                            "tipo" => "hidden",
                            "etichetta" => "Id",
                            "readonly" => true
                            ),
                            array(
                            "campo" => "dataagg",
                            "tipo" => "hidden",
                            "etichetta" => "Dataagg",
                            "readonly" => true
                            ),
                            array(
                            "campo" => "scrittore",
                            "tipo" => "hidden",
                            "etichetta" => "Scrittore",
                            "readonly" => true
                            ),
                            array(
                                "campo" => "stato",
                                "tipo" => "select_static",
                                "etichetta" => "Stato",
                                "readonly" => false,
                                "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                            ),
                            array(
                                "campo" => "livello",
                                "tipo" => "select2",
                                "etichetta" => "Livello",
                                "readonly" => false,
                                "sql" => "SELECT nome as valore, nome FROM lista_utenti_livelli WHERE stato='Attivo'"
                            ),
                            array(
                            "campo" => "nome",
                            "tipo" => "input",
                            "etichetta" => "Nome",
                            "readonly" => false
                            ),
                            array(
                            "campo" => "descrizione",
                            "tipo" => "input",
                            "etichetta" => "Descrizione",
                            "readonly" => false
                            ),
                            array(
                            "campo" => "colore_testo",
                            "tipo" => "select_colore",
                            "etichetta" => "Colore testo",
                            "readonly" => false
                            ),
                            array(
                            "campo" => "colore_sfondo",
                            "tipo" => "select_colore",
                            "etichetta" => "Colore sfondo",
                            "readonly" => false
                            ),
                            array(
                            "campo" => "colore_esadecimale",
                            "tipo" => "select_colore",
                            "etichetta" => "Colore esadecimale",
                            "readonly" => false
                            ))
                    );

/** TABELLA LISTA_RICHIESTE_STATI **/
$table_listaRichiesteStati = array(
                "index" => array("campi" => "
                                CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_richieste_stati&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_richieste_stati&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_richieste_stati&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                nome as 'Nome', descrizione as 'Descrizione', stato as 'Stato', livello AS 'Livello', colore_sfondo as 'Colore',
                                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_richieste_stati&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => "1 ".$where_lista_richieste_stati,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true
                ),
                array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true
                ),
                array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true
                ),
                array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                ),
                array(
                    "campo" => "livello",
                    "tipo" => "select2",
                    "etichetta" => "Livello",
                    "readonly" => false,
                    "sql" => "SELECT nome as valore, nome FROM lista_utenti_livelli WHERE stato='Attivo'"
                ),
                array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false
                ),
                array(
                "campo" => "descrizione",
                "tipo" => "input",
                "etichetta" => "Descrizione",
                "readonly" => false
                ),
                array(
                "campo" => "colore_testo",
                "tipo" => "select_colore",
                "etichetta" => "Colore testo",
                "readonly" => false
                ),
                array(
                "campo" => "colore_sfondo",
                "tipo" => "select_colore",
                "etichetta" => "Colore sfondo",
                "readonly" => false
                ),
                array(
                "campo" => "colore_esadecimale",
                "tipo" => "select_colore",
                "etichetta" => "Colore esadecimale",
                "readonly" => false
                ))
        );

/** TABELLA LISTA_PREVENTIVI **/
$table_listaPreventivi = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printPreventivoPDF.php?id=',id,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"inviaPrev.php?idPrev=',id,'\" data-target=\"#ajax\" data-url=\"inviaPrev.php?idPrev=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
                                            data_iscrizione,
                                            LEFT(data_creazione,10) AS creato_il,
                                            IF(codice LIKE 'xxx',CONCAT('<small>',codice_interno,'</small>'),CONCAT('<B>',codice,'/',sezionale,'</B>')) AS `Codice`,
                                            IF(id_professionista<=0,(SELECT CONCAT('<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i> ',mittente,'') FROM calendario WHERE id = id_calendario),(SELECT CONCAT('<CENTER><b>',cognome,' ',nome,'</b></CENTER>') FROM lista_professionisti WHERE id=`id_professionista`)) AS `Professionista`,
                                            imponibile AS 'Imponibile &euro;',
                                            stato,
                                            (SELECT CONCAT(cognome,' ',nome) AS UTENTE FROM lista_password WHERE id=id_agente) AS `Agente`",
                                            //IF(`stato` = 'Chiuso','<a href=\"#\" title=\"CHIUSO\" alt=\"CHIUSO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinoverdek.png\"></a>',IF(`stato` = 'In Attesa',CONCAT('<a href=\"#\" title=\"IN ATTESA\" alt=\"IN ATTESA\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinogiallok.png\"></a>'),IF(`stato` = 'Negativo','<a href=\"#\" title=\"NEGATIVO\" alt=\"NEGATIVO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinorossok.png\"></a>',stato))) AS stato_prev",
                                "where" => "1 ".$where_lista_preventivi,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "sezionale",
                        "tipo" => "select2",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM lista_fatture_sezionali WHERE LENGTH(nome)>1 AND (stato='Attivo' OR stato LIKE 'Predefinito') ORDER BY nome ASC"
                    ),
                    /*array(  "campo" => "id_azienda",
                        "tipo" => "select2",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, ragione_sociale AS nome FROM lista_aziende WHERE LENGTH(ragione_sociale)>1 ORDER BY ragione_sociale ASC"
                    ),*/
                /*array(  "campo" => "id_professionista",
                        "tipo" => "select2",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_professionisti WHERE 1 ORDER BY cognome, nome ASC"
                    ),*/
                    array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false
                    ),
                     array(  "campo" => "note",
                        "tipo" => "text",
                        "etichetta" => "Note",
                        "readonly" => false
                    )),
            "esporta" => array( 
                    array( "campo" => "dataagg",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Ultimo Aggiornamento",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "data_iscrizione",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Iscritto/Negativo",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "data_firma",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Firma/Chiuso",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "cognome_nome_professionista",
                        "tipo" => "input",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "ragione_sociale_azienda",
                        "tipo" => "input",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "id_agente",
                        "tipo" => "select2",
                        "etichetta" => "Commerciale",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true,
                        "sql" => "SELECT id as valore, CONCAT(lista_password.cognome,' ',lista_password.nome) AS nome FROM lista_password WHERE lista_password.stato='Attivo' AND lista_password.livello='commerciale' ORDER BY lista_password.cognome ASC, lista_password.nome ASC "
                    ),
                    array(  "campo" => "codice_ricerca",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "sezionale",
                        "tipo" => "input",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "imponibile",
                        "tipo" => "input",
                        "etichetta" => "Imponibile",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "importo",
                        "tipo" => "input",
                        "etichetta" => "Importo",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "iva",
                        "tipo" => "input",
                        "etichetta" => "Iva",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "Stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true,
                        "sql" => array("In Attesa" => "In Attesa", "Venduto"=>"Venduto", "Negativo"=>"Negativo", "Chiuso"=>"Chiuso")
                    ),
                    array(  "campo" => "nome_campagna",
                        "tipo" => "input",
                        "etichetta" => "Campagna",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array("campo" => "id_provvigione",
                        "tipo" => "inner_select",
                        "etichetta" => "Codice Partner",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT GROUP_CONCAT(lista_provvigioni.codice SEPARATOR '<br>') FROM lista_provvigioni WHERE lista_provvigioni.id IN (SELECT lista_preventivi_dettaglio.id_provvigione FROM lista_preventivi_dettaglio WHERE lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id) ORDER BY lista_provvigioni.codice ASC)",
                        "attivo" => true
                    ),
                    array("campo" => "email_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Email Professionista",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT email AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "indirizzo_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Indirizzo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT CONCAT(lista_aziende.indirizzo,' ',lista_aziende.cap,' ',lista_aziende.citta,' (',lista_aziende.provincia,')') AS Indirizzo FROM lista_aziende WHERE lista_aziende.id=lista_preventivi.id_azienda)",
                        "attivo" => true
                    ),
                    array("campo" => "elenco_prodotti",
                        "tipo" => "inner_select",
                        "etichetta" => "Prodotti",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "CONCAT((SELECT GROUP_CONCAT(lista_preventivi_dettaglio.nome_prodotto,' (', lista_preventivi_dettaglio.codice_prodotto ,')' SEPARATOR '<br>') FROM lista_preventivi_dettaglio WHERE lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id))",
                        "attivo" => true
                    ),
                    array("campo" => "professione",
                        "tipo" => "inner_select",
                        "etichetta" => "professione",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT professione AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "provincia_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Provincia Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT provincia_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "numero_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Numero Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT numero_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)",
                        "attivo" => true
                    )
                    )
            );

/*
 * $table_listaPreventiviDettaglio
 */
$table_listaPreventiviDettaglio = array(
                "index" => array("campi" => "CONCAT('<a href=\"modifica.php?tbl=lista_preventivi_dettaglio&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS 'fa-edit',
                        CONCAT('<a href=\"cancella.php?tbl=lista_preventivi_dettaglio&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><button type=\"button\" class=\"btn red btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-trash\"></i></button></a>') AS 'fa-trash',
                        dataagg, codice_preventivo, nome_prodotto, prezzo_prodotto, iva_prodotto, quantita",
                                "where" => "1 ".$where_lista_preventivi_dettaglio,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                    array(  "campo" => "id_preventivo",
                        "tipo" => "hidden",
                        "etichetta" => "id_preventivo",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice_preventivo",
                        "tipo" => "input",
                        "etichetta" => "Cod. Prev.",
                        "readonly" => true
                    ),
                array(  "campo" => "nome_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Prodotto",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, prezzo_pubblico AS var_1, iva AS var_2, quantita AS var_3 FROM lista_prodotti WHERE LENGTH(nome)>1 ORDER BY nome ASC",
                        "ajax" => "scriviDentroListaPreventiviDettaglio"
                    ),
                array(  "campo" => "prezzo_prodotto",
                        "tipo" => "numerico",
                        "etichetta" => "Prezzo",
                        "readonly" => false
                    ),
                    array(  "campo" => "iva_prodotto",
                        "tipo" => "numerico",
                        "etichetta" => "Iva",
                        "readonly" => false
                    ),
                    array(  "campo" => "quantita",
                        "tipo" => "numerico",
                        "etichetta" => "Qnt.",
                        "readonly" => false
                    )
                    )
            );
 /*
 * $table_listaFatture

 CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
 CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"inviaFatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',


 */
$table_listaFatture = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                             CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
		CONCAT('<span class=\"btn sbold uppercase btn-outline blue-chambray\">',codice_ricerca,'</span>') AS codice,
		CONCAT('',IF(id_azienda>0,(SELECT CONCAT('<b>',ragione_sociale,' ',forma_giuridica,'</b>') FROM lista_aziende WHERE id=`id_azienda`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Azienda non presente nel database!\\');\"></i>'),'',IF(id_professionista>0,(SELECT CONCAT('<p>',cognome,' ',nome,'</p>') FROM lista_professionisti WHERE id=`id_professionista`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i>'),'') AS 'Contatto',
		CONCAT(IF(id_fatture_banche>0,(SELECT DISTINCT nome FROM lista_fatture_banche WHERE id=id_fatture_banche),''),'<br>',pagamento) AS 'pagamento',
		DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creata', data_creazione as data_sort, DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y') AS 'Scadenza',
		imponibile AS 'Imponibile &euro;',
		stato",
                                "where" => " 1 ".$where_lista_fatture,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_scadenza",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_sezionale",
                        "tipo" => "select2",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_sezionali` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "id_fatture_banche",
                        "tipo" => "select2",
                        "etichetta" => "Banca",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_banche` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "pagamento",
                        "tipo" => "select2",
                        "etichetta" => "Pagamento",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM `lista_tipologie_pagamento` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "stato",
                        "tipo" => "input",
                        "etichetta" => "Stato",
                        "readonly" => true
                        ),
                    array(  "campo" => "nota_documento",
                        "tipo" => "text",
                        "etichetta" => "Nota Documento",
                        "readonly" => false
                        ))
            );

/* LISTA FATTURE RECUPERO CREDITI */
$table_listaFattureRecuperoCrediti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                                            CONCAT('<span class=\"btn sbold uppercase btn-outline blue-chambray\">',codice_ricerca,'</span>') AS codice,
                                            CONCAT('',IF(id_azienda>0,(SELECT CONCAT('<b>',ragione_sociale,' ',forma_giuridica,'</b>') FROM lista_aziende WHERE id=`id_azienda`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Azienda non presente nel database!\\');\"></i>'),'',IF(id_professionista>0,(SELECT CONCAT('<p>',cognome,' ',nome,'</p>') FROM lista_professionisti WHERE id=`id_professionista`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i>'),'') AS 'Contatto',
                                            CONCAT(IF(id_fatture_banche>0,(SELECT DISTINCT nome FROM lista_fatture_banche WHERE id=id_fatture_banche),''),'<br>',pagamento) AS 'pagamento',
                                            DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creata',
                                            imponibile AS 'Imponibile &euro;',
                                            (SELECT CONCAT(lista_password.cognome,' ',lista_password.nome) as commerciale FROM lista_password WHERE lista_password.id = lista_fatture.id_agente) as Commerciale,
                                            (SELECT lista_professionisti.email FROM lista_professionisti WHERE id = lista_fatture.id_professionista) as Email,
                                            (SELECT CONCAT('Tel: ',lista_professionisti.telefono,'<br>Cel: ',lista_professionisti.cellulare) AS telefono FROM lista_professionisti WHERE id = lista_fatture.id_professionista) as Telefono
                                            ",
                                "where" => " stato='In Attesa' ".$where_lista_fatture,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_scadenza",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_sezionale",
                        "tipo" => "select2",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_sezionali` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "id_fatture_banche",
                        "tipo" => "select2",
                        "etichetta" => "Banca",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_banche` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "pagamento",
                        "tipo" => "select2",
                        "etichetta" => "Pagamento",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM `lista_tipologie_pagamento` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "stato",
                        "tipo" => "input",
                        "etichetta" => "Stato",
                        "readonly" => true
                        ))
            );
	/**
LISTA_FATTURE_DETTAGLIO
				*/
$table_listaFattureDettaglio = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id_fattura,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                             CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id_fattura,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
		CONCAT('<span class=\"btn sbold uppercase btn-outline blue-chambray\">',codice_fattura,'</span>') AS codice,
		CONCAT('',IF(id_azienda>0,(SELECT CONCAT('<b>',ragione_sociale,' ',forma_giuridica,'</b>') FROM lista_aziende WHERE id=`id_azienda`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Azienda non presente nel database!\\');\"></i>'),'',IF(id_professionista>0,(SELECT CONCAT('<p>',cognome,' ',nome,'</p>') FROM lista_professionisti WHERE id=`id_professionista`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i>'),'') AS 'Contatto', stato",
                                "where" => " 1 ".$where_lista_fatture_dettaglio,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Prodotto",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_prodotti` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
					array(  "campo" => "prezzo_prodotto",
                        "tipo" => "input",
                        "etichetta" => "Imponibile",
                        "readonly" => false
                    ),
					array(  "campo" => "iva_prodotto",
                        "tipo" => "input",
                        "etichetta" => "Iva",
                        "readonly" => false
                    ))
            );
 /*
 lista_fatture_multiple
 */
 $table_listaFattureMultiple = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture_multiple&idProfessionista=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                (SELECT CONCAT('<CENTER><b>',cognome,' ',nome,'</b></CENTER>') FROM lista_professionisti WHERE id=`id_professionista`) AS Professionista,
		stato",
                                "where" => "1 ".$where_lista_fatture,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "id_azienda",
                        "tipo" => "select2",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, ragione_sociale AS nome FROM lista_aziende WHERE LENGTH(ragione_sociale)>1 ORDER BY ragione_sociale ASC"
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "select2",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_professionisti WHERE 1 ORDER BY cognome, nome ASC"
                    ))
            );
			

			
 /*
 lista_fatture da emettere multiplo
 */			
$table_listaFattureEmettiMultiplo = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                             CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
		data_preventivo, sezionale, 
		CONCAT('',IF(id_azienda>0,(SELECT CONCAT('<b>',ragione_sociale,' ',forma_giuridica,'</b>') FROM lista_aziende WHERE id=`id_azienda`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Azienda non presente nel database!\\');\"></i>'),'',IF(id_professionista>0,(SELECT CONCAT('<p>',cognome,' ',nome,'</p>') FROM lista_professionisti WHERE id=`id_professionista`),'<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i>'),'') AS 'Contatto',
		CONCAT(IF(id_fatture_banche>0,(SELECT DISTINCT nome FROM lista_fatture_banche WHERE id=id_fatture_banche),''),'<br>',pagamento) AS 'pagamento',
		imponibile AS 'Imponibile &euro;',
		stato",
                                "where" => " 1 ".$where_lista_fatture,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_scadenza",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_sezionale",
                        "tipo" => "select2",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_sezionali` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "id_fatture_banche",
                        "tipo" => "select2",
                        "etichetta" => "Banca",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_banche` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "pagamento",
                        "tipo" => "select2",
                        "etichetta" => "Pagamento",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM `lista_tipologie_pagamento` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "id_azienda",
                        "tipo" => "select2",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, ragione_sociale AS nome FROM lista_aziende WHERE LENGTH(ragione_sociale)>1 ORDER BY ragione_sociale ASC"
                    ),
                    array(  "campo" => "id_professionista",
                        "tipo" => "select2",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_professionisti WHERE 1 ORDER BY cognome, nome ASC"
                    ),
                    array(  "campo" => "stato",
                        "tipo" => "bs-select",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM `lista_fatture_stati` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ))
            );
 /*
 lista_fatture_multiple
 */
 $table_listaFattureMultiple = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture_multiple&idProfessionista=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                (SELECT CONCAT('<CENTER><b>',cognome,' ',nome,'</b></CENTER>') FROM lista_professionisti WHERE id=`id_professionista`) AS Professionista,
		stato",
                                "where" => "1 ".$where_lista_fatture,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "id_azienda",
                        "tipo" => "select2",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, ragione_sociale AS nome FROM lista_aziende WHERE LENGTH(ragione_sociale)>1 ORDER BY ragione_sociale ASC"
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "select2",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_professionisti WHERE 1 ORDER BY cognome, nome ASC"
                    ))
            );
 /*
 * $table_listaCampagne
 SELECT `id`, `dataagg`, `scrittore`, `tipo`, `nome`, `descrizione`, `data_inizio`, `data_fine`, `durata`, `id_tipo_marketing`, `id_prodotto`, `note`,
FROM `lista_campagne` WHERE 1
 */
$table_listaCampagne = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_campagne&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            IF(id > 99,CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_campagne&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_campagne&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                            CONCAT('<span class=\"btn btn-lg sbold uppercase btn-outline blue-madison\">',id,'</span>') AS 'Codice',
                                            CONCAT('<span class=\"btn sbold uppercase btn-outline blue-dark\">',`nome`,'</span>') AS Nome,
                                            (SELECT DISTINCT CONCAT('<B>',`nome`,'</b>') FROM `lista_tipo_marketing` WHERE `id` = `id_tipo_marketing`) AS 'Marketing',
                                             (SELECT DISTINCT CONCAT('<B>',`nome` ,'</B><BR><SMALL>',codice,'</SMALL>')FROM `lista_prodotti` WHERE `id` = `id_prodotto`) AS 'Prodotto',
                                             CONCAT('Inizio: ',`data_inizio`,'<br>Fine: ',`data_fine`) AS Tempo,
                                             numerico_5 AS '%',
                                            (SELECT CONCAT(`colore_sfondo`,'|', `nome`) FROM lista_campagne_stati WHERE lista_campagne_stati.`nome` LIKE `lista_campagne`.`stato`) AS `stato` ",
                                "where" => "1 ".$where_lista_campagne,
                                "order" => "ORDER BY numerico_5 DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                    array(  "campo" => "descrizione",
                        "tipo" => "text",
                        "etichetta" => "Descrizione",
                        "readonly" => false
                    ),
                array(  "campo" => "id_tipo_marketing",
                        "tipo" => "select2",
                        "etichetta" => "Marketing",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM  `lista_tipo_marketing`  WHERE LENGTH(`nome`)>1 AND  `stato` LIKE 'Attivo' ORDER BY  `nome`  ASC"
                    ),
                array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Prodotto",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_prodotti`  WHERE LENGTH(`nome`)>1 AND  `stato` LIKE 'Attivo' ORDER BY  `nome`  ASC"
                    ),
                    array(  "campo" => "prezzo_sconto",
                        "tipo" => "input",
                        "etichetta" => "Prezzo Scontato",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_inizio",
                        "tipo" => "data",
                        "etichetta" => "Data Inizio",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => false
                    ),
                    array(  "campo" => "url_1",
                        "tipo" => "input",
                        "etichetta" => "Url",
                        "readonly" => false
                    ),
                    array(  "campo" => "campo_5",
                        "tipo" => "input",
                        "etichetta" => "Codice Adwords",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_sezionale",
                        "tipo" => "select-cancella",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_sezionali` WHERE `stato` LIKE 'Attivo' ORDER BY `nome` ASC"
                    ),
                    array(  "campo" => "stato",
                        "tipo" => "bs-select",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM  `lista_campagne_stati` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    )
                    )
            );



 /*
 * $table_listaProdotti
 SELECT `id`, `id_area`, `cliente`, `tipo`, `nome`, `descrizione`, `descrizione_breve`, `marchio`, `fornitore`, `gruppo`,
 `categoria`, `tipologia`, `note`, `dataagg`, `scrittore`, `codice`, `codice_interno`, `codice_esterno`, `id_prodotto_0`, `num_prg`, `bolla`,
 `barcode`, `quantita`, `unita_misura`, `tipo_materiale`, `costo`, `costo_standard`, `costo_ultimo`, `costo_fornitore`, `categoria_statistica`,
 `prezzo_pubblico`, `prezzo_min`, `prezzo_max`, `iva`, `stato`, `tempo_lavorazione`, `id_operatore`, `costo_lavorazione`, `tempo_1`, `tempo_2`,
 `utente_1`, `utente_2`, `id_agente`, `cognome_nome_agente`, `gruppo_agente`
 FROM `lista_prodotti` WHERE 1
 */
$table_listaProdotti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_prodotti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            gruppo, tipologia,
                                            CONCAT('<B>',`nome`,'</b>') AS 'Prodotto',
                                            codice,
                                            codice_esterno AS ID_Moodle,
                                            `prezzo_pubblico` AS 'Prezzo €',
                                            `stato` ",
                                "where" => "1 ".$where_lista_prodotti,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                    array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => false
                    ),
                    /*array(  "campo" => "codice_esterno",
                        "tipo" => "input",
                        "etichetta" => "ID Moodle",
                        "readonly" => false
                    ),*/
                    array(  "campo" => "descrizione",
                        "tipo" => "htmlarea",
                        "etichetta" => "Descrizione",
                        "readonly" => false
                    ),
                /*array(  "campo" => "marchio",
                        "tipo" => "bs-select",
                        "etichetta" => "Marchio",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM  `lista_prodotti_marchi` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    ),*/
                /*array(  "campo" => "categoria",
                        "tipo" => "bs-select",
                        "etichetta" => "Categoria",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM  `lista_prodotti_categorie` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    ),*/
                    array(  "campo" => "descrizione_fattura",
                        "tipo" => "input",
                        "etichetta" => "Descrizione Fattura",
                        "readonly" => false
                    ),
                    array(  "campo" => "tipologia",
                        "tipo" => "bs-select",
                        "etichetta" => "Tipologia",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM  `lista_prodotti_tipologie` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    ),
                    array(  "campo" => "gruppo",
                        "tipo" => "bs-select",
                        "etichetta" => "Gruppo",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome, colore_sfondo AS colore FROM  `lista_prodotti_gruppi` WHERE  `stato` LIKE 'Attivo' ORDER BY  `nome` ASC"
                    ),
                    array(  "campo" => "quantita",
                        "tipo" => "numerico",
                        "etichetta" => "Quantità di default",
                        "readonly" => false
                    ),
                     array(  "campo" => "prezzo_pubblico",
                        "tipo" => "numerico",
                        "etichetta" => "Prezzo al Pubblico",
                        "readonly" => false
                    ),
                    array(  "campo" => "prezzo_min",
                        "tipo" => "numerico",
                        "etichetta" => "Prezzo Scontato",
                        "readonly" => false
                    ),
                    array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                        )
                    )
            );

/*
LISTA_CORSI
*/
$table_listaCorsi = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_corsi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<H3>',nome_prodotto,'</H3>') AS 'Corso',
                                            (SELECT DISTINCT codice FROM lista_prodotti WHERE id = id_prodotto) AS 'Codice',
                                            (SELECT DISTINCT codice_esterno FROM lista_prodotti WHERE id = id_prodotto) AS 'ID MOODLE',
                                            LEFT(SEC_TO_TIME(`durata`),8) AS 'Durata',
                                            `stato` ",
                                "where" => "1 ".$where_lista_corsi,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Prodotto",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_prodotti`  WHERE LENGTH(`nome`)>1 AND  `stato` LIKE 'Attivo' ORDER BY  `nome`  ASC"
                    ),
                    array(  "campo" => "data_creazione_corso",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione Corso",
                        "readonly" => false
                    ),
                    array(  "campo" => "nome_docente",
                        "tipo" => "input",
                        "etichetta" => "Docente",
                        "readonly" => false
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );
/*
LISTA_CORSI_DETTAGLIO
*/
$table_listaCorsiDettaglio = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi_dettaglio&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<H4>',`name`,'</H4>') AS 'Nome',
            `modname` AS 'Tipo', durata,
            IF(`visible`>=1,'Attivo','Non Attivo') AS 'Stato',
             `ordine`, `id_modulo`,
              `instance` AS 'ISTANCE MOODLE'",
                                "where" => "1 ".$where_lista_corsi_dettaglio,
                                "order" => "ORDER BY ordine ASC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                     array(  "campo" => "name",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => true
                    ),
                    array(  "campo" => "durata",
                        "tipo" => "input",
                        "etichetta" => "Durata",
                        "readonly" => false
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );
/*
LISTA_ISCRIZIONI

CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
           (SELECT DISTINCT `nome_prodotto` FROM lista_corsi WHERE id = id_corso) AS 'Corso',
           data_inizio, data_fine, stato,
           (SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe) AS 'Classe',
            (SELECT DISTINCT CONCAT(cognome, ' ', nome)  FROM lista_professionisti WHERE id = id_professionista) AS 'Profes.'


*/
$table_listaIscrizioni = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni&id=',id_corso,'&idCorso=',id_corso,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi&id=',id_corso,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            (SELECT DISTINCT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
                                            (SELECT DISTINCT lista_prodotti.codice FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'Codice',
                                             (SELECT DISTINCT lista_prodotti.codice_esterno FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'ID MOODLE'",
                                "where" => "1 ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );

$table_listaIscrizioniInCorso = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni&id=',id_corso,'&idCorso=',id_corso,'&whrStato=1\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi&id=',id_corso,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            (SELECT DISTINCT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
                                            (SELECT DISTINCT lista_prodotti.codice FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'Codice',
                                             (SELECT DISTINCT lista_prodotti.codice_esterno FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'ID MOODLE'",
                                "where" => " stato='In Corso' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );

$table_listaIscrizioniInAttesa = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni&id=',id_corso,'&idCorso=',id_corso,'&whrStato=2\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi&id=',id_corso,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            (SELECT DISTINCT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
                                            (SELECT DISTINCT lista_prodotti.codice FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'Codice',
                                             (SELECT DISTINCT lista_prodotti.codice_esterno FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'ID MOODLE'",
                                "where" => " stato='In Attesa' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );

$table_listaIscrizioniCompletati = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni&id=',id_corso,'&idCorso=',id_corso,'&whrStato=3\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi&id=',id_corso,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            (SELECT DISTINCT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
                                            (SELECT DISTINCT lista_prodotti.codice FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'Codice',
                                             (SELECT DISTINCT lista_prodotti.codice_esterno FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto
                                            WHERE lista_corsi.id = id_corso) AS 'ID MOODLE'",
                                "where" => " stato='Completato' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                    )
            );
/*
LISTA_ISCRIZIONI X PARTECIPANTI

            CONCAT('<small>dal ',data_inizio_iscrizione,' al ',data_fine_iscrizione,'</small>') AS 'Validit&agrave;',
            CONCAT('<small>dal ',data_inizio,' al ',data_fine,'</small>') AS 'In Corso',

*/
$table_listaIscrizioniPartecipanti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/iscrizioni/dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>',
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>',
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')
                ) AS 'Tipo',
                data_inizio,
             IF(id_professionista>0,(SELECT DISTINCT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3><small>', IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),'') ,'</small>')  FROM lista_professionisti WHERE id = id_professionista), CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\"></i><br>', cognome_nome_professionista,'<br><small>',IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),''),'</small>')) AS 'Partecipante',
             (SELECT DISTINCT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
            stato,
            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.'",
                                "where" => " stato='In Corso' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
            "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "nome_corso",
                    "tipo" => "input",
                    "etichetta" => "Corso",
                    "readonly" => true
                ),
                array(  "campo" => "cognome_nome_professionista",
                    "tipo" => "input",
                    "etichetta" => "Professionista",
                    "readonly" => true
                ),
                array(  "campo" => "nome_classe",
                    "tipo" => "input",
                    "etichetta" => "Classe",
                    "readonly" => true
                ),
                array(  "campo" => "data_inizio_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Attivazione",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Scadenza",
                    "readonly" => false
                ),
                array(  "campo" => "data_inizio",
                    "tipo" => "data",
                    "etichetta" => "Data Inizio",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine",
                    "tipo" => "data",
                    "etichetta" => "Data Fine",
                    "readonly" => true
                ),
                array(  "campo" => "avanzamento_completamento",
                    "tipo" => "input",
                    "etichetta" => "Avanzamento %",
                    "readonly" => true
                ),
                array(  "campo" => "id_fattura",
                    "tipo" => "select2",
                    "etichetta" => "Fattura Collegata",
                    "readonly" => false,
                    "sql" => "SELECT id as valore, CONCAT(codice,'".SEPARATORE_FATTURA."',sezionale,' del ',DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y'),' a: ',lista_fatture.cognome_nome_professionista) AS nome 
                                FROM lista_fatture 
                                WHERE 
                                (
                                    lista_fatture.id_professionista IN (SELECT lista_iscrizioni.id_professionista FROM lista_iscrizioni WHERE id = '".(isset($_GET['id']) ? $_GET['id'] : 0 )."') 
                                )
                                    OR (
                                        LCASE(lista_fatture.cognome_nome_professionista) LIKE (SELECT CONCAT('%',LCASE(SUBSTRING_INDEX(lista_iscrizioni.cognome_nome_professionista,' ',1)),'%') FROM lista_iscrizioni WHERE id = '".(isset($_GET['id']) ? $_GET['id'] : 0 )."')
                                    )"
                ),
                array(  "campo" => "id_classe",
                    "tipo" => "select2",
                    "etichetta" => "Classe",
                    "readonly" => false,
                    "sql" => "SELECT id as valore, nome AS nome FROM lista_classi WHERE stato = 'Attivo'"
                ),
                /*array(
                "campo" => "stato",
                "tipo" => "select_static",
                "etichetta" => "Stato",
                "readonly" => false,
                "sql" => array("In Attesa"=>"In Attesa", "In Corso"=>"In Corso")
                )*/
                ),
            "esporta" => array(
                array(  "campo" => "nome_corso",
                        "tipo" => "input",
                        "etichetta" => "Corso",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                ),
                array(  "campo" => "cognome_nome_professionista",
                        "tipo" => "input",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                ),
                array(  "campo" => "id_classe",
                    "tipo" => "select2",
                    "etichetta" => "Nome Classe",
                    "readonly" => false,
                    "like" => false,
                    "uguale" => true,
                    "maggiore" => false,
                    "default" => "",
                    "attivo" => false,
                    "sql" => "SELECT id as valore, nome AS nome FROM lista_classi WHERE 1 ORDER BY nome ASC"
                ),
                /*array(  "campo" => "nome_classe",
                        "tipo" => "input",
                        "etichetta" => "Classe",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                ),*/
                array(  "campo" => "data_inizio_iscrizione",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Attivazione",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "data_fine_iscrizione",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                array(  "campo" => "data_inizio",
                        "tipo" => "data",
                        "etichetta" => "Data Inizio",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                ),
                array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                ),
                array(  "campo" => "data_completamento",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Completamento",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                ),
                array(  "campo" => "avanzamento_completamento",
                        "tipo" => "input",
                        "etichetta" => "Avanzamento %",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                ),
                
                
                
                array(  "campo" => "Stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true,
                        "sql" => array("Completato" => "Completato", "In Corso"=>"In Corso", "In Attesa"=>"In Attesa", "Configurazione"=>"Configurazione", "Scaduto e Disattivato"=>"Scaduto e Disattivato", "Configurazione Scaduta e Disattivata"=>"Configurazione Scaduta e Disattivata")
                    ),
                    array("campo" => "id_provvigione",
                        "tipo" => "inner_select",
                        "etichetta" => "Codice Partner",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT GROUP_CONCAT(lista_provvigioni.codice SEPARATOR '<br>') FROM lista_provvigioni WHERE lista_provvigioni.id IN (SELECT lista_fatture_dettaglio.id_provvigione FROM lista_fatture_dettaglio WHERE lista_fatture_dettaglio.id_preventivo = lista_iscrizioni.id_fattura_dettaglio) ORDER BY lista_provvigioni.codice ASC)",
                        "attivo" => true
                    ),
                    array("campo" => "email_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Email Professionista",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT email AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "cellulare_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Cellulare Professionista",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT IF(LENGTH(cellulare) > 3, cellulare, telefono) AS cellulare_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "provincia_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Provincia di Nascita",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT provincia_di_nascita AS provincia_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "professione",
                        "tipo" => "inner_select",
                        "etichetta" => "professione",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT professione AS professione_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "provincia_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Provincia Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT provincia_albo AS provincia_albo_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "numero_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Numero Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT numero_albo AS numero_albo_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_iscrizioni.id_professionista)",
                        "attivo" => true
                    )
            )
        );
/*
LISTA_ISCRIZIONI X PARTECIPANTI

            CONCAT('<small>dal ',data_inizio_iscrizione,' al ',data_fine_iscrizione,'</small>') AS 'Validit&agrave;',
            CONCAT('<small>dal ',data_inizio,' al ',data_fine,'</small>') AS 'In Corso',

*/
$table_listaIscrizioniPartecipantiCompletati = array(
                "index" => array("campi" => "
                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',id,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>',
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>', 
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                                            DATE_FORMAT(data_inizio,'%d-%m-%Y') AS data_inizio,
                                            DATE_FORMAT(data_completamento,'%d-%m-%Y') AS data_completamento,
                                            (SELECT CONCAT(cognome, ' ', nome) AS nome FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Partecipante,
                                            (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS Classe,
                                            (SELECT codice_fiscale FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Codice_Fiscale,
                                            (SELECT professione FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS professione,
                                            (SELECT numero_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Iscirizone_Ordine,
                                            (SELECT provincia_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provincia_Ordine,
                                            (SELECT email FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Email,
                                            (SELECT luogo_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS luogo_di_nascita,
                                            (SELECT DATE_FORMAT(data_di_nascita,'%d-%m-%Y') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Data_di_Nascita,
                                            (SELECT provincia_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provoncia_di_Nascita,
                                            (SELECT nome_prodotto FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Corso',
                                            (SELECT codice FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Codice Corso',
                                            IF(id_fattura>0,
                                                (SELECT if(stato LIKE 'Pagata%','<span class=\"btn sbold uppercase btn-outline green-jungle\">SI</span>','<span class=\"btn sbold uppercase btn-outline red-intense\">NO</span>') FROM lista_fatture WHERE id = lista_iscrizioni.id_fattura AND tipo  LIKE 'Fattura' AND sezionale NOT LIKE '%CN%' LIMIT 1),
                                                '<span class=\"btn sbold uppercase btn-outline yellow-saffron\">FATTURA NON ASSOCIATA</span>'
                                            ) AS 'Fattura Pagata',
                                            stato,
                                            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.'
                                            ",
                                "where" => " stato='Completato' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
            "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "nome_corso",
                    "tipo" => "input",
                    "etichetta" => "Corso",
                    "readonly" => true
                ),
                array(  "campo" => "cognome_nome_professionista",
                    "tipo" => "input",
                    "etichetta" => "Professionista",
                    "readonly" => true
                ),
                array(  "campo" => "nome_classe",
                    "tipo" => "input",
                    "etichetta" => "Classe",
                    "readonly" => true
                ),
                array(  "campo" => "data_inizio_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Attivazione",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Scadenza",
                    "readonly" => false
                ),
                array(  "campo" => "data_inizio",
                    "tipo" => "data",
                    "etichetta" => "Data Inizio",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine",
                    "tipo" => "data",
                    "etichetta" => "Data Fine",
                    "readonly" => true
                ),
                array(  "campo" => "avanzamento_completamento",
                    "tipo" => "input",
                    "etichetta" => "Avanzamento %",
                    "readonly" => true
                )
                )
        );

$table_listaIscrizioniPartecipantiCompletatiPagati = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue-steel btn-outline\" href=\"".BASE_URL."/moduli/corsi/dettaglio.php?tbl=lista_corsi&id=',id_corso,'\" title=\"CONFIGURAZIONE\" alt=\"CONFIGURAZIONE\" target=\"_blank\"><i class=\"fa fa-cogs\"></i></a>') AS 'fa-cogs',
                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',id,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>',
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>',
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                                            (SELECT CONCAT('<span class=\"btn sbold uppercase btn-outline blue-steel\">',cognome,' ',nome,'</span>') AS nome FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Partecipante,
                                            (SELECT CONCAT('<span class=\"btn sbold uppercase btn-outline blue-steel\">',nome_prodotto,'</span>') AS nome_prodotto FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Corso',
                                            DATE_FORMAT(data_completamento,'%d-%m-%Y') AS data_completamento,
                                            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.',
                                            IF(id_fattura>0,
                                                (SELECT IF(sezionale LIKE '%CN%','<span class=\"btn sbold uppercase btn-outline red\">CN</span>',if(stato LIKE 'Pagata%','<span class=\"btn sbold uppercase btn-outline green-jungle\">SI</span>','<span class=\"btn sbold uppercase btn-outline red-intense\">NO</span>')) FROM lista_fatture WHERE id = lista_iscrizioni.id_fattura AND tipo  LIKE 'Fattura' LIMIT 1),
                                                '<span class=\"btn sbold uppercase btn-outline yellow-saffron\">FATTURA NON ASSOCIATA</span>'
                                            ) AS 'Fattura Pagata',
                                            stato,
                                            (SELECT codice FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Codice Corso',
                                            DATE_FORMAT(data_inizio,'%d-%m-%Y') AS data_inizio,
                                            (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS Classe,
                                            (SELECT codice_fiscale FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Codice_Fiscale,
                                            (SELECT professione FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS professione,
                                            (SELECT numero_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Iscirizone_Ordine,
                                            (SELECT provincia_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provincia_Ordine,
                                            (SELECT email FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Email,
                                            (SELECT luogo_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS luogo_di_nascita,
                                            (SELECT DATE_FORMAT(data_di_nascita,'%d-%m-%Y') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Data_di_Nascita,
                                            (SELECT provincia_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provincia_di_Nascita",
                                "where" => " stato='Completato' AND id_fattura IN (SELECT id FROM lista_fatture WHERE stato LIKE 'Pagata%') AND data_completamento >= '2017-10-18' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
            "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "nome_corso",
                    "tipo" => "input",
                    "etichetta" => "Corso",
                    "readonly" => true
                ),
                array(  "campo" => "cognome_nome_professionista",
                    "tipo" => "input",
                    "etichetta" => "Professionista",
                    "readonly" => true
                ),
                array(  "campo" => "nome_classe",
                    "tipo" => "input",
                    "etichetta" => "Classe",
                    "readonly" => true
                ),
                array(  "campo" => "data_inizio_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Attivazione",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Scadenza",
                    "readonly" => false
                ),
                array(  "campo" => "data_inizio",
                    "tipo" => "data",
                    "etichetta" => "Data Inizio",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine",
                    "tipo" => "data",
                    "etichetta" => "Data Fine",
                    "readonly" => true
                ),
                array(  "campo" => "avanzamento_completamento",
                    "tipo" => "input",
                    "etichetta" => "Avanzamento %",
                    "readonly" => true
                )
                )
        );
        
$table_listaIscrizioniPartecipantiCompletatiNonPagati = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue-steel btn-outline\" href=\"".BASE_URL."/moduli/corsi/dettaglio.php?tbl=lista_corsi&id=',id_corso,'\" title=\"CONFIGURAZIONE\" alt=\"CONFIGURAZIONE\" target=\"_blank\"><i class=\"fa fa-cogs\"></i></a>') AS 'fa-cogs',
                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',id,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>', 
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>', 
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                                            (SELECT CONCAT('<span class=\"btn sbold uppercase btn-outline blue-steel\">',cognome,' ',nome,'</span>') AS nome FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Partecipante,
                                            (SELECT CONCAT('<span class=\"btn sbold uppercase btn-outline blue-steel\">',nome_prodotto,'</span>') AS nome_prodotto FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Corso',
                                            DATE_FORMAT(data_completamento,'%d-%m-%Y') AS data_completamento,
                                            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.',
                                            IF(id_fattura>0,
                                                (SELECT IF(sezionale LIKE '%CN%','<span class=\"btn sbold uppercase btn-outline red\">CN</span>',if(stato LIKE 'Pagata%','<span class=\"btn sbold uppercase btn-outline green-jungle\">SI</span>','<span class=\"btn sbold uppercase btn-outline red-intense\">NO</span>')) FROM lista_fatture WHERE id = lista_iscrizioni.id_fattura AND tipo  LIKE 'Fattura' LIMIT 1),
                                                '<span class=\"btn sbold uppercase btn-outline yellow-saffron\">FATTURA NON ASSOCIATA</span>'
                                            ) AS 'Fattura Pagata',
                                            stato,
                                            (SELECT codice FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Codice Corso',
                                            DATE_FORMAT(data_inizio,'%d-%m-%Y') AS data_inizio,
                                            (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS Classe,
                                            (SELECT codice_fiscale FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Codice_Fiscale,
                                            (SELECT professione FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS professione,
                                            (SELECT numero_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Iscirizone_Ordine,
                                            (SELECT provincia_albo FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provincia_Ordine,
                                            (SELECT email FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Email,
                                            (SELECT luogo_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS luogo_di_nascita,
                                            (SELECT DATE_FORMAT(data_di_nascita,'%d-%m-%Y') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Data_di_Nascita,
                                            (SELECT provincia_di_nascita FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS Provincia_di_Nascita",
                                "where" => " stato='Completato' AND id_fattura IN (SELECT id FROM lista_fatture WHERE stato LIKE 'In Attesa%') AND data_completamento >= '2017-10-18' ".$where_lista_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
            "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "nome_corso",
                    "tipo" => "input",
                    "etichetta" => "Corso",
                    "readonly" => true
                ),
                array(  "campo" => "cognome_nome_professionista",
                    "tipo" => "input",
                    "etichetta" => "Professionista",
                    "readonly" => true
                ),
                array(  "campo" => "nome_classe",
                    "tipo" => "input",
                    "etichetta" => "Classe",
                    "readonly" => true
                ),
                array(  "campo" => "data_inizio_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Attivazione",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine_iscrizione",
                    "tipo" => "data",
                    "etichetta" => "Data Scadenza",
                    "readonly" => false
                ),
                array(  "campo" => "data_inizio",
                    "tipo" => "data",
                    "etichetta" => "Data Inizio",
                    "readonly" => true
                ),
                array(  "campo" => "data_fine",
                    "tipo" => "data",
                    "etichetta" => "Data Fine",
                    "readonly" => true
                ),
                array(  "campo" => "avanzamento_completamento",
                    "tipo" => "input",
                    "etichetta" => "Avanzamento %",
                    "readonly" => true
                )
                )
        );
        
/*
LISTA_ISCRIZIONI X CONFIGURAZIONE

            CONCAT('<small>dal ',data_inizio_iscrizione,' al ',data_fine_iscrizione,'</small>') AS 'Validit&agrave;',
            CONCAT('<small>dal ',data_inizio,' al ',data_fine,'</small>') AS 'In Corso',

*/
$table_listaIscrizioniConfigurazioni = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>', 
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>', 
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                data_fine_iscrizione,
                IF(id_professionista>0,(SELECT DISTINCT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3>') FROM lista_professionisti WHERE id = id_professionista), CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\"></i><br>', cognome_nome_professionista,'<br><small>',IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),''),'</small>')) AS 'Partecipante',
                IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),'') AS Classe,
                stato",
                "where" => " (stato='Configurazione' OR stato='Abbonamento Disabilitato') AND data_fine_iscrizione >= NOW() ".$where_lista_iscrizioni_configurazione,
                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome_corso",
                        "tipo" => "input",
                        "etichetta" => "Corso",
                        "readonly" => true
                    ),
                    array(  "campo" => "cognome_nome_professionista",
                        "tipo" => "input",
                        "etichetta" => "Professionista",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome_classe",
                        "tipo" => "input",
                        "etichetta" => "Classe",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_inizio_iscrizione",
                        "tipo" => "data",
                        "etichetta" => "Data Attivazione",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_fine_iscrizione",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_inizio",
                        "tipo" => "data",
                        "etichetta" => "Data Inizio",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => true
                    ),
                    array(  "campo" => "avanzamento_completamento",
                        "tipo" => "input",
                        "etichetta" => "Avanzamento %",
                        "readonly" => true
                    ),
                    /*array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("In Attesa"=>"In Attesa", "In Corso"=>"In Corso")
                    )*/
                    )
            );

$table_listaIscrizioniControlloDoppi = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>', 
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Pacchetto</span>', 
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                data_fine_iscrizione,
                IF(id_professionista>0,(SELECT DISTINCT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3>') FROM lista_professionisti WHERE id = id_professionista), CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\"></i><br>', cognome_nome_professionista,'<br><small>',IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),''),'</small>')) AS 'Partecipante',
                IF(id_classe>0,(SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe),'') AS Classe,
                stato",
                "where" => " (stato='Configurazione' OR stato='Abbonamento Disabilitato') AND data_fine_iscrizione >= NOW() ".$where_lista_iscrizioni_configurazione,
                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome_corso",
                        "tipo" => "input",
                        "etichetta" => "Corso",
                        "readonly" => true
                    ),
                    array(  "campo" => "cognome_nome_professionista",
                        "tipo" => "input",
                        "etichetta" => "Professionista",
                        "readonly" => true
                    ),
                    array(  "campo" => "nome_classe",
                        "tipo" => "input",
                        "etichetta" => "Classe",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_inizio_iscrizione",
                        "tipo" => "data",
                        "etichetta" => "Data Attivazione",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_fine_iscrizione",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "data_inizio",
                        "tipo" => "data",
                        "etichetta" => "Data Inizio",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => true
                    ),
                    array(  "campo" => "avanzamento_completamento",
                        "tipo" => "input",
                        "etichetta" => "Avanzamento %",
                        "readonly" => true
                    ),
                    /*array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("In Attesa"=>"In Attesa", "In Corso"=>"In Corso")
                    )*/
                    )
            );

/*
LISTA_PASSWORD X COMMERCIALI
`id`, `dataagg`, `id_professionista`, `livello`, `nome`, `cognome`,
`username`, `passwd`, `passwd_email`, `cellulare`, `email`, `stato`,
`numerico_1`, `numerico_2`, `numerico_3`, `numerico_4`, `numerico_5`,
`nickname`, `avatar`, `id_moodle_user`
*/
$table_listaCommerciali = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            `livello`, `nome`, `cognome`, `cellulare`, `email`, `stato`",
                                "where" => " livello LIKE 'commerciale'  ".$where_lista_password,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "hidden",
                        "etichetta" => "ID Professionista",
                        "readonly" => true
                    ),
                /*array(  "campo" => "data_creazione",
                        "tipo" => "hidden",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),*/
                    array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "livello",
                        "tipo" => "select2",
                        "etichetta" => "Livello",
                        "readonly" => false,
                        "sql" => "SELECT nome as valore, nome FROM lista_utenti_livelli WHERE stato='Attivo' AND nome!='amministratore'"
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "cognome",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "username",
                        "tipo" => "username",
                        "etichetta" => "Nome Utente",
                        "readonly" => false
                    ),
                array(  "campo" => "passwd",
                        "tipo" => "password",
                        "etichetta" => "Password",
                        "readonly" => false
                    ),
                array(  "campo" => "passwd_email",
                        "tipo" => "passwd_email",
                        "etichetta" => "Password E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "nickname",
                        "tipo" => "input",
                        "etichetta" => "Nickname",
                        "readonly" => false
                    ),
                array(  "campo" => "avatar",
                        "tipo" => "file",
                        "etichetta" => "File Avatar",
                        "readonly" => false,
                        "dir" => BASE_ROOT."media/users/"
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ),
                    array(  "campo" => "id_moodle_user",
                        "tipo" => "numerico",
                        "etichetta" => "ID MOODLE",
                        "readonly" => true
                    ))
            );

/*
LISTA_CLASSI
*/
$table_listaClassi = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            `nome`, `codice`, `codice_esterno`, `stato`",
                                "where" => "1 ".$where_lista_classi,
                                "order" => "ORDER BY nome DESC"),
                "modifica" => array(
                    array(
                    "campo" => "id",
                    "tipo" => "hidden",
                    "etichetta" => "Id",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "dataagg",
                    "tipo" => "hidden",
                    "etichetta" => "Dataagg",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "scrittore",
                    "tipo" => "hidden",
                    "etichetta" => "Scrittore",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "stato",
                    "tipo" => "select_static",
                    "etichetta" => "Stato",
                    "readonly" => false,
                    "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ),
                    array(
                    "campo" => "nome",
                    "tipo" => "input",
                    "etichetta" => "Nome",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "codice",
                    "tipo" => "input",
                    "etichetta" => "Codice",
                    "readonly" => false
                    ),
                    array(
                    "campo" => "codice_esterno",
                    "tipo" => "numerico",
                    "etichetta" => "Codice esterno",
                    "readonly" => false
                    )
                )
            );

/**
TABELLA LISTA_ORDINI
lista_ordini
**/
$table_listaOrdini = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_ordini&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            data_creazione AS 'Creato_il',
                                            data_iscrizione AS 'Iscritto_il',
                                            (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista) AS 'Utente',
                                            (SELECT CONCAT(ragione_sociale, ' ', forma_giuridica) FROM lista_aziende WHERE id = id_azienda) AS 'Azienda',
                                            imponibile AS 'Imponibile &euro;',
                                            stato",
                                            //IF(`stato` = 'Chiuso','<a href=\"#\" title=\"CHIUSO\" alt=\"CHIUSO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinoverdek.png\"></a>',IF(`stato` = 'In Attesa',CONCAT('<a href=\"#\" title=\"IN ATTESA\" alt=\"IN ATTESA\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinogiallok.png\"></a>'),IF(`stato` = 'Negativo','<a href=\"#\" title=\"NEGATIVO\" alt=\"NEGATIVO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinorossok.png\"></a>',stato))) AS stato_prev",
                                "where" => "1 ".$where_lista_ordini,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "sezionale",
                        "tipo" => "select2",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "sql" => "SELECT nome AS valore, nome AS nome FROM lista_fatture_sezionali WHERE LENGTH(nome)>1 AND (stato='Attivo' OR stato LIKE 'Predefinito') ORDER BY nome ASC"
                    ),
                    array(  "campo" => "id_azienda",
                        "tipo" => "select2",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, ragione_sociale AS nome FROM lista_aziende WHERE LENGTH(ragione_sociale)>1 ORDER BY ragione_sociale ASC"
                    ),
                array(  "campo" => "id_professionista",
                        "tipo" => "select2",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, CONCAT(cognome,' ', nome) AS nome FROM lista_professionisti WHERE 1 ORDER BY cognome, nome ASC"
                    )),
                "esporta" => array( 
                    array( "campo" => "dataagg",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Ultimo Aggiornamento",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "data_iscrizione",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Iscritto/Negativo",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "data_firma",
                        "tipo" => "confronto_data",
                        "etichetta" => "Data Firma/Chiuso",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "id_agente",
                        "tipo" => "inner_select",
                        "etichetta" => "Commerciale",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "sql" => "",
                        "attivo" => true,
                        "default" => "(SELECT CONCAT(lista_password.cognome,' ',lista_password.nome) AS nome FROM lista_password WHERE lista_password.id = lista_ordini.id_agente)"
                    ),
                    array(  "campo" => "sezionale",
                        "tipo" => "input",
                        "etichetta" => "Sezionale",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "imponibile",
                        "tipo" => "input",
                        "etichetta" => "Imponibile",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "importo",
                        "tipo" => "input",
                        "etichetta" => "Importo",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "iva",
                        "tipo" => "input",
                        "etichetta" => "Iva",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => true
                    ),
                    array(  "campo" => "Stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => true,
                        "sql" => array("In Corso" => "In Corso", "Chiuso"=>"Chiuso")
                    ),
                    array(  "campo" => "id_campagna",
                            "tipo" => "select2",
                            "etichetta" => "Nome Campagna",
                            "readonly" => false,
                            "like" => false,
                            "uguale" => true,
                            "maggiore" => false,
                            "sql" => "SELECT id AS valore, nome AS nome FROM lista_campagne WHERE UPPER(nome) LIKE '%SHOP%'",
                            "attivo" => true,
                            "default" => ""
                    ),
                    array("campo" => "id_azienda",
                        "tipo" => "inner_select",
                        "etichetta" => "Azienda",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT CONCAT(lista_aziende.ragione_sociale, ' ', lista_aziende.forma_giuridica) FROM lista_aziende WHERE lista_aziende.id = lista_ordini.id_azienda)",
                        "attivo" => true
                    ),
                    array("campo" => "id_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Professionista",
                        "readonly" => true,
                        "like" => false,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT CONCAT(lista_professionisti.cognome, ' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_ordini.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "email_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Email Professionista",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT email AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_ordini.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "indirizzo_professionista",
                        "tipo" => "inner_select",
                        "etichetta" => "Indirizzo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT CONCAT(lista_aziende.indirizzo,' ',lista_aziende.cap,' ',lista_aziende.citta,' (',lista_aziende.provincia,')') AS Indirizzo FROM lista_aziende WHERE lista_aziende.id=lista_ordini.id_azienda)",
                        "attivo" => true
                    ),
                    array("campo" => "elenco_prodotti",
                        "tipo" => "inner_select",
                        "etichetta" => "Prodotti",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "CONCAT((SELECT GROUP_CONCAT(lista_ordini_dettaglio.nome_prodotto,' (', lista_ordini_dettaglio.codice_prodotto ,')' SEPARATOR '<br>') FROM lista_ordini_dettaglio WHERE lista_ordini_dettaglio.id_ordine = lista_ordini.id))",
                        "attivo" => true
                    ),
                    array("campo" => "professione",
                        "tipo" => "inner_select",
                        "etichetta" => "professione",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT professione AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_ordini.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "provincia_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Provincia Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT provincia_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_ordini.id_professionista)",
                        "attivo" => true
                    ),
                    array("campo" => "numero_albo",
                        "tipo" => "inner_select",
                        "etichetta" => "Numero Albo",
                        "readonly" => true,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "(SELECT numero_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_ordini.id_professionista)",
                        "attivo" => true
                    )
                    )
            );
/*
*   `id`, `id_area`, `dataagg`, `data_creazione`, `data_scadenza`, `id_fattura`, `id_commessa`,
`id_preventivo`, `id_prodotto`, `id_contatto`, `id_professionista`, `id_azienda`, `id_documento`, `tipo_documento`, `categoria`,
 `descrizione`, `entrate`, `uscite`, `imponibile`, `iva`, `imposta`, `note`, `scrittore`, `tipo`, `stato`,
 `campo_1`, `campo_2`, `campo_3`, `campo_4`, `campo_5`

`lista_costi`
*/
$table_listaCosti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_costi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_costi&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
                                            DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creazione',
                                            (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista ) AS 'Professionista',
                                            (SELECT ragione_sociale FROM lista_aziende WHERE id = id_azienda ) AS 'Azienda',
                                            IF(entrate>0,CONCAT('<FONT COLOR=\"GREEN\">',`entrate`,'</FONT>'),'') AS 'entrate',
                                            IF(uscite>0,CONCAT('<FONT COLOR=\"RED\">',`uscite`,'</FONT>'),'') AS 'uscite',
                                            IF(uscite>0,CONCAT('<h6 class=\"font-red-pink\">',0-(entrate-uscite),' &euro;</h4>'),'') AS 'Differenza',
                                            nome_banca AS 'Banca', stato",
                                            //IF(`stato` = 'Chiuso','<a href=\"#\" title=\"CHIUSO\" alt=\"CHIUSO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinoverdek.png\"></a>',IF(`stato` = 'In Attesa',CONCAT('<a href=\"#\" title=\"IN ATTESA\" alt=\"IN ATTESA\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinogiallok.png\"></a>'),IF(`stato` = 'Negativo','<a href=\"#\" title=\"NEGATIVO\" alt=\"NEGATIVO\" class=\"smallButton\" style=\"margin: 5px;\"><img src=\"images/pallinorossok.png\"></a>',stato))) AS stato_prev",
                                "where" => "1 ".$where_lista_costi,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "input",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                     array(  "campo" => "stato",
                        "tipo" => "input",
                        "etichetta" => "Stato",
                        "readonly" => true
                    ),
                     array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false
                    ),
                     array(  "campo" => "data_scadenza",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => false
                    ),
                    array(  "campo" => "descrizione",
                        "tipo" => "text",
                        "etichetta" => "Descrizione",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_fatture_banche",
                        "tipo" => "select2",
                        "etichetta" => "Banca",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM `lista_fatture_banche` WHERE stato LIKE 'Attivo' ORDER BY nome ASC"
                    ),
                    array(  "campo" => "entrate",
                        "tipo" => "input",
                        "etichetta" => "Entrate / Avere",
                        "readonly" => false
                    ),
                     array(  "campo" => "uscite",
                        "tipo" => "input",
                        "etichetta" => "Uscite / Dare",
                        "readonly" => false
                    )),
                    "esporta" => array(
                array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => false,
                        "like" => false,
                        "uguale" => true,
                        "maggiore" => true,
                        "default" => "",
                        "attivo" => false
                    ),
                     array(  "campo" => "stato",
                        "tipo" => "input",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                    array(  "campo" => "cognome_nome_professionista",
                        "tipo" => "input",
                        "etichetta" => "Professionista",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    ),
                    array(  "campo" => "ragione_sociale_azienda",
                        "tipo" => "input",
                        "etichetta" => "Azienda",
                        "readonly" => false,
                        "like" => true,
                        "uguale" => false,
                        "maggiore" => false,
                        "default" => "",
                        "attivo" => false
                    )
                    )
            );
/** TABELLA CALENDARIO X ESAMI **/
$table_calendarioEsami = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario_esami&id=',id,'&idProdotto=',id_prodotto,'',IF(etichetta LIKE 'Calendario Esami','&esame=1','&esame=0'),'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario_esami&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                             ora, data,
                                            IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo',
                                            CONCAT('<B>',oggetto,'</B>') AS Oggetto, 
                                            IF(id_aula>0, (SELECT nome FROM lista_aule WHERE id = id_aula),'') AS 'Aula',
                                            (SELECT COUNT(*) FROM matrice_corsi_docenti WHERE matrice_corsi_docenti.id_calendario = calendario.id AND calendario.id_prodotto = matrice_corsi_docenti.id_prodotto) AS 'N. Docente',
                                            
                                            CONCAT('Aula: ',numerico_4,'<br>Docenti: ',numerico_5,'<br>Extra: ',numerico_3) AS 'Costi', 
                                            campo_13 AS 'Durata Corso',
                                            numerico_10 AS 'Iscritti', stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                            //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                "where" => " 1 AND (etichetta LIKE 'Calendario Esami' OR etichetta LIKE 'Calendario Corsi') ".$where_calendario_esami,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "etichetta",
                        "tipo" => "select_static",
                        "etichetta" => "Etichetta",
                        "readonly" => false,
                        "sql" => array("Calendario Esami"=>"Calendario Esami", "Calendario Corsi"=>"Calendario Corsi")
                    ),
                    array(  "campo" => "id_prodotto",
                        "tipo" => "select2",
                        "etichetta" => "Corso",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_prodotti WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                array(  "campo" => "data",
                        "tipo" => "data",
                        "etichetta" => "Data",
                        "readonly" => false
                    ),
                array(  "campo" => "ora",
                        "tipo" => "ora",
                        "etichetta" => "Ora",
                        "readonly" => false
                    ),
                array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => false
                    ),
                array(  "campo" => "ora_fine",
                        "tipo" => "ora",
                        "etichetta" => "Ora Fine",
                        "readonly" => false
                    ),
                array(  "campo" => "oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto",
                        "readonly" => false
                    ),
                array(  "campo" => "messaggio",
                        "tipo" => "text",
                        "etichetta" => "Messaggio",
                        "readonly" => false
                    ),
                    array(  "campo" => "id_aula",
                        "tipo" => "select2",
                        "etichetta" => "Aula",
                        "readonly" => false,
                        "sql" => "SELECT id AS valore, nome AS nome FROM lista_aule WHERE stato='Attivo' ORDER BY nome ASC"
                    ),
                     array(  "campo" => "numerico_3",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Extra",
                        "readonly" => false
                    ),
                    array(  "campo" => "numerico_4",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Aula",
                        "readonly" => false
                    ),
                    array(  "campo" => "numerico_5",
                        "tipo" => "numerico",
                        "etichetta" => "Costo Docenti",
                        "readonly" => false
                    ),
                    array(  "campo" => "campo_13",
                        "tipo" => "numerico",
                        "etichetta" => "Durata Corso",
                        "readonly" => false
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
            );
            
/** TABELLA CALENDARIO X ISCRIZIONE ESAMI **/
$table_calendarioEsamiIscrizioni = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario_esami&id=',id,'&idCorso=',id_corso,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario_esami&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            data, ora, oggetto, numerico_10 AS 'Iscritti', stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                            //CONCAT('link:dettaglio.php?tbl=calendario&id=',id,'|icona:fa fa-search font-yellow|nome:Dettaglio||link:modifica.php?tbl=calendario&id=',id,'|icona:fa fa-edit font-blue|nome:Modifica||divider||link:cancella.php?tbl=calendario&id=',id,'|icona:fa fa-trash font-red|nome:Elimina') AS Azioni,
                                            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=calendario&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS '.:',
                                "where" => " 1 AND etichetta LIKE 'Iscrizione Esame' ".$where_calendario_esami_iscrizioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                    array(  "campo" => "etichetta",
                        "tipo" => "select_static",
                        "etichetta" => "Etichetta",
                        "readonly" => false,
                        "sql" => array("Calendario Esami"=>"Calendario Esami")
                    ),
                    array(  "campo" => "id_corso",
                        "tipo" => "select2",
                        "etichetta" => "Corso",
                        "readonly" => true,
                        "sql" => "SELECT id AS valore, nome_prodotto AS nome FROM lista_corsi WHERE stato='Attivo' ORDER BY nome_prodotto ASC"
                    ),
                array(  "campo" => "data",
                        "tipo" => "data",
                        "etichetta" => "Data",
                        "readonly" => true
                    ),
                array(  "campo" => "ora",
                        "tipo" => "ora",
                        "etichetta" => "Ora",
                        "readonly" => true
                    ),
                array(  "campo" => "data_fine",
                        "tipo" => "data",
                        "etichetta" => "Data Fine",
                        "readonly" => true
                    ),
                array(  "campo" => "ora_fine",
                        "tipo" => "ora",
                        "etichetta" => "Ora Fine",
                        "readonly" => true
                    ),
                array(  "campo" => "oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto",
                        "readonly" => true
                    ),
                array(  "campo" => "messaggio",
                        "tipo" => "text",
                        "etichetta" => "Messaggio",
                        "readonly" => false
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Iscritto"=>"Iscritto", "Non Iscritto"=>"Non Iscritto", "Presente"=>"Presente", "Non Presente"=>"Non Presente", "Promosso"=>"Promosso", "Non Promosso"=>"Non Promosso")
                    ))
            );
/*
 * $table_listaFatture
 */
$table_listaFattureInvioMultiplo = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',id,'&idA=',id_area,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                                            DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creata', DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y') AS 'Scadenza',
		CONCAT('<b>',`codice`,'".SEPARATORE_FATTURA."', sezionale ,'</b>') AS codice,
		CONCAT('<center>',(SELECT CONCAT('<b>',ragione_sociale,'</b>') FROM lista_aziende WHERE id=`id_azienda`),'<br><small>',
		(SELECT CONCAT('',cognome,' ',nome,'') FROM lista_professionisti WHERE id=`id_professionista`)
		,'</small></center>') AS 'Azienda / Professionista',
		imponibile AS 'Imponibile &euro;',
		CONCAT('<CENTER><SMALL>',data_invio,'</SMALL></CENTER>') AS 'Inviata',
		stato, id AS selezione",
                                "where" => "1 ".$where_lista_fatture." AND stato LIKE 'In Attesa' AND stato_invio NOT LIKE 'In Attesa di Invio'",
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                     array(  "campo" => "data_creazione",
                        "tipo" => "data",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                    array(  "campo" => "data_scadenza",
                        "tipo" => "data",
                        "etichetta" => "Data Scadenza",
                        "readonly" => true
                    ),
                    array(  "campo" => "stato",
                        "tipo" => "input",
                        "etichetta" => "Stato",
                        "readonly" => true
                    )
                    )
            );
/*
 * $table_documentiAttestati -> lista_documenti X ATTESTATI

 `id`, `dataagg`, `data_creazione`, `id_fattura`, `id_commessa`, `id_commessa_dettaglio`, `id_preventivo`,
 `id_prodotto`, `id_contatto`, `id_bolla`, `id_costo`, `tipo_documento`, `categoria`, `nome`, `descrizione`, `estensione`, `orientamento`,
 `note`, `scrittore`, `stato`, `tabella`, `id_corso`, `id_classe`

 * CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_attestati&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
   CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printAttestatoPDF_',orientamento,'.php?idAttestato=',id,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                                            
 * 
 *  */

$table_documentiAttestati = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_attestati&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_attestati&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
                                            nome, orientamento, tipo_documento AS 'Tipo', descrizione",
                                "where" => "1 ".$where_lista_attestati,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                     array(  "campo" => "nome",
                        "tipo" => "file",
                        "etichetta" => "File",
                        "readonly" => true,
                        "dir" => BASE_ROOT."moduli/corsi/"
                    ),
                    array(  "campo" => "tipo_documento",
                        "tipo" => "input",
                        "etichetta" => "Tipo",
                        "readonly" => false
                    ),
                    array(  "campo" => "descrizione",
                        "tipo" => "text",
                        "etichetta" => "Descrizione",
                        "readonly" => false
                    ),
                    array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ),
                   array(  "campo" => "orientamento",
                        "tipo" => "select_static",
                        "etichetta" => "Orientamento",
                        "readonly" => false,
                        "sql" => array("L"=>"Orizzontale", "P"=>"Verticale")
                    ))
            );


$table_listaProdottiCategorie = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti_categorie&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_prodotti_categorie&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                            nome, descrizione, colore_testo, colore_sfondo, stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_prodotti_categorie&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => " 1 ".$where_lista_prodotti_categorie,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
	array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true),
	array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true),
	array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true),
	array(
                "campo" => "stato",
                "tipo" => "select_static",
                "etichetta" => "Stato",
                "readonly" => false,
                "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                ),
	array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false),
	array(
                "campo" => "descrizione",
                "tipo" => "input",
                "etichetta" => "Descrizione",
                "readonly" => false),
	array(
                "campo" => "colore_testo",
                "tipo" => "input",
                "etichetta" => "Colore testo",
                "readonly" => false),
	array(
                "campo" => "colore_sfondo",
                "tipo" => "input",
                "etichetta" => "Colore sfondo",
                "readonly" => false)
	/*array(
                "campo" => "colore_esadecimale",
                "tipo" => "input",
                "etichetta" => "Colore esadecimale",
                "readonly" => false),
		)*/
                )
        );


$table_listaProdottiTipologie = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti_tipologie&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_prodotti_tipologie&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                            nome, descrizione, colore_testo, colore_sfondo, stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_prodotti_tipologie&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => " 1 ".$where_lista_prodotti_tipologie,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
	array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true),
	array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true),
	array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true),
	array(
                "campo" => "stato",
                "tipo" => "select_static",
                "etichetta" => "Stato",
                "readonly" => false,
                "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                ),
	array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false),
	array(
                "campo" => "descrizione",
                "tipo" => "input",
                "etichetta" => "Descrizione",
                "readonly" => false),
	array(
                "campo" => "colore_testo",
                "tipo" => "input",
                "etichetta" => "Colore testo",
                "readonly" => false),
	array(
                "campo" => "colore_sfondo",
                "tipo" => "input",
                "etichetta" => "Colore sfondo",
                "readonly" => false)
	/*array(
                "campo" => "colore_esadecimale",
                "tipo" => "input",
                "etichetta" => "Colore esadecimale",
                "readonly" => false),
		)*/
                )
        );

$table_listaProdottiGruppi = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti_gruppi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_prodotti_gruppi&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                            nome, descrizione, colore_testo, colore_sfondo, stato,
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_prodotti_gruppi&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'",
                                "where" => " 1 ".$where_lista_prodotti_gruppi,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
	array(
                "campo" => "id",
                "tipo" => "hidden",
                "etichetta" => "Id",
                "readonly" => true),
	array(
                "campo" => "dataagg",
                "tipo" => "hidden",
                "etichetta" => "Dataagg",
                "readonly" => true),
	array(
                "campo" => "scrittore",
                "tipo" => "hidden",
                "etichetta" => "Scrittore",
                "readonly" => true),
	array(
                "campo" => "stato",
                "tipo" => "select_static",
                "etichetta" => "Stato",
                "readonly" => false,
                "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                ),
	array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false),
	array(
                "campo" => "descrizione",
                "tipo" => "input",
                "etichetta" => "Descrizione",
                "readonly" => false),
	array(
                "campo" => "colore_testo",
                "tipo" => "input",
                "etichetta" => "Colore testo",
                "readonly" => false),
	array(
                "campo" => "colore_sfondo",
                "tipo" => "input",
                "etichetta" => "Colore sfondo",
                "readonly" => false)
	/*array(
                "campo" => "colore_esadecimale",
                "tipo" => "input",
                "etichetta" => "Colore esadecimale",
                "readonly" => false),
		)*/
                )
        );
$table_listaTemplateEmail = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_template_email&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_template_email&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_template_email&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash' , stato
, nome
, mittente
, reply
, destinatario
, cc
, bcc
, oggetto
, messaggio
, allegato_1
, allegato_2
, allegato_3
",
"where" => " 1 ".$where_lista_template_email,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(

    array(
                "campo" => "stato",
                "tipo" => "numerico",
                "etichetta" => "Stato",
                "readonly" => false),
    array(
                "campo" => "nome",
                "tipo" => "input",
                "etichetta" => "Nome",
                "readonly" => false),
    array(
                "campo" => "nome_alias",
                "tipo" => "input",
                "etichetta" => "Nome Alias",
                "readonly" => false),
    array(
                "campo" => "mittente",
                "tipo" => "input",
                "etichetta" => "Mittente",
                "readonly" => false),
    array(
                "campo" => "reply",
                "tipo" => "input",
                "etichetta" => "Reply",
                "readonly" => false),
    array(
                "campo" => "destinatario",
                "tipo" => "input",
                "etichetta" => "Destinatario",
                "readonly" => false),
    array(
                "campo" => "cc",
                "tipo" => "input",
                "etichetta" => "Cc",
                "readonly" => false),
    array(
                "campo" => "bcc",
                "tipo" => "input",
                "etichetta" => "Bcc",
                "readonly" => false),
    array(
                "campo" => "oggetto",
                "tipo" => "input",
                "etichetta" => "Oggetto",
                "readonly" => false),
    array(
                "campo" => "messaggio",
                "tipo" => "htmlarea",
                "etichetta" => "Messaggio",
                "readonly" => false),
    array(
                "campo" => "allegato_1",
                "tipo" => "input",
                "etichetta" => "Allegato 1",
                "readonly" => false),
    array(
                "campo" => "allegato_2",
                "tipo" => "input",
                "etichetta" => "Allegato 2",
                "readonly" => false),
    array(
                "campo" => "allegato_3",
                "tipo" => "input",
                "etichetta" => "Allegato 3",
                "readonly" => false),
        )
     );
/** TABELLA LISTA_DOCENTI **/
$table_listaDocenti = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_docenti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_docenti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<b>',`cognome`,' ',`nome`,'</b>') AS 'Docente', codice_fiscale, cellulare, telefono, email, stato",
                                "where" => " 1 ".$where_lista_docenti,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "data_creazione",
                        "tipo" => "hidden",
                        "etichetta" => "Data Creazione",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome",
                        "readonly" => false
                    ),
                array(  "campo" => "cognome",
                        "tipo" => "input",
                        "etichetta" => "Cognome",
                        "readonly" => false
                    ),
                array(  "campo" => "data_di_nascita",
                        "tipo" => "data",
                        "etichetta" => "Data di nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "luogo_di_nascita",
                        "tipo" => "input",
                        "etichetta" => "Luogo di nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "provincia_di_nascita",
                        "tipo" => "input",
                        "etichetta" => "Provincia di Nascita",
                        "readonly" => false
                    ),
                array(  "campo" => "codice_fiscale",
                        "tipo" => "codice_fiscale",
                        "etichetta" => "Codice Fiscale",
                        "readonly" => false
                    ),
                array(  "campo" => "telefono",
                        "tipo" => "telefono",
                        "etichetta" => "Telefono",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "fax",
                        "tipo" => "fax",
                        "etichetta" => "Fax",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "web",
                        "tipo" => "web",
                        "etichetta" => "Sito Web",
                        "readonly" => false
                    ),
                array(  "campo" => "note",
                        "tipo" => "text",
                        "etichetta" => "Note",
                        "readonly" => false
                    ),array(
                        "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
            );
/** TABELLA LISTA_AULE **/
$table_listaAule = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_aule&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_aule&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<b>',`nome`,'</b>') AS 'Aula', indirizzo, cap, citta, provincia, telefono, stato",
                                "where" => " 1 ".$where_lista_aule,
                                "order" => "ORDER BY id DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                array(  "campo" => "codice",
                        "tipo" => "input",
                        "etichetta" => "Codice",
                        "readonly" => true
                    ),
                array(  "campo" => "codice_esterno",
                        "tipo" => "input",
                        "etichetta" => "Codice Est.",
                        "readonly" => true
                    ),
                array(  "campo" => "nome",
                        "tipo" => "input",
                        "etichetta" => "Nome Aula",
                        "readonly" => false
                    ),
                array(  "campo" => "indirizzo",
                        "tipo" => "indirizzo",
                        "etichetta" => "Indirizzo",
                        "readonly" => false
                    ),
                array(  "campo" => "cap",
                        "tipo" => "cap",
                        "etichetta" => "CAP",
                        "readonly" => false
                    ),
                array(  "campo" => "citta",
                        "tipo" => "input",
                        "etichetta" => "Città",
                        "readonly" => false
                    ),
                array(  "campo" => "provincia",
                        "tipo" => "input",
                        "etichetta" => "Prov.",
                        "readonly" => false
                    ),
                array(  "campo" => "nazione",
                        "tipo" => "input",
                        "etichetta" => "Nazione",
                        "readonly" => false
                    ),
                array(  "campo" => "telefono",
                        "tipo" => "telefono",
                        "etichetta" => "Telefono",
                        "readonly" => false
                    ),
                array(  "campo" => "cellulare",
                        "tipo" => "cellulare",
                        "etichetta" => "Cellulare",
                        "readonly" => false
                    ),
                array(  "campo" => "fax",
                        "tipo" => "fax",
                        "etichetta" => "Fax",
                        "readonly" => false
                    ),
                array(  "campo" => "email",
                        "tipo" => "email",
                        "etichetta" => "E-Mail",
                        "readonly" => false
                    ),
                array(  "campo" => "web",
                        "tipo" => "web",
                        "etichetta" => "Sito Web",
                        "readonly" => false
                    ),
                array(  "campo" => "tipo",
                        "tipo" => "select_static",
                        "etichetta" => "Tipo",
                        "readonly" => false,
                        "sql" => array("DECIDERE 1"=>"DECIDERE 1", "DECIDERE 2"=>"DECIDERE 2")
                    ),
                array(  "campo" => "note",
                        "tipo" => "text",
                        "etichetta" => "Note",
                        "readonly" => false
                    ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    ))
            );
$table_listaCorsiConfigurazioni = array(
                "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
                                            dataagg, scrittore, stato",
                                "where" => "1 ".$where_lista_corsi_configurazioni,
                                "order" => "ORDER BY dataagg DESC"),
                "modifica" => array(
                array(  "campo" => "id",
                        "tipo" => "hidden",
                        "etichetta" => "ID",
                        "readonly" => true
                    ),
                array(  "campo" => "dataagg",
                        "tipo" => "hidden",
                        "etichetta" => "Data Agg.",
                        "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                        "tipo" => "hidden",
                        "etichetta" => "Scrittore",
                        "readonly" => true
                    ),
                     array(  "campo" => "titolo",
                        "tipo" => "input",
                        "etichetta" => "Titolo",
                        "readonly" => false
                    ),
                    array(  "campo" => "email_mittente",
                        "tipo" => "input",
                        "etichetta" => "Email Mittente",
                        "readonly" => false
                    ),
                    array(  "campo" => "firma",
                        "tipo" => "text",
                        "etichetta" => "Luogo e Data Documento",
                        "readonly" => false
                    ),
                    array(  "campo" => "email_oggetto",
                        "tipo" => "input",
                        "etichetta" => "Oggetto E-Mail",
                        "readonly" => false
                    ),
                    array(  "campo" => "messaggio",
                        "tipo" => "htmlarea",
                        "etichetta" => "Corpo Documento",
                        "readonly" => false
                    ),
                    array(  "campo" => "email_messaggio",
                        "tipo" => "htmlarea",
                        "etichetta" => "Messaggio E-Mail",
                        "readonly" => false
                    ))
            );


$table_listaProvvigioni = array(
            "index" => array("campi" => "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_provvigioni&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_provvigioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=lista_provvigioni&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
                                        CONCAT('<span class=\"btn btn-lg sbold uppercase btn-outline blue-madison\">',codice,'</span>') AS 'Codice',
                                        CONCAT('<span class=\"btn sbold uppercase btn-outline blue-dark\">',`nome`,'</span>') AS Nome,
                                        (SELECT DISTINCT CONCAT('<B>',`nome` ,'</B><BR><SMALL>',codice,'</SMALL>')FROM `lista_prodotti` WHERE `id` = `id_prodotto`) AS 'Prodotto',
                                        prezzo_sconto,
                                        provvigione AS 'Provvigione &euro;',
                                        provvigione_percentuale AS 'Provvigione %',
                                        `stato` ",
                            "where" => "1 ".$where_lista_provvigioni,
                            "order" => "ORDER BY codice DESC"),
            "modifica" => array(
            array(  "campo" => "id",
                    "tipo" => "hidden",
                    "etichetta" => "ID",
                    "readonly" => true
                ),
            array(  "campo" => "dataagg",
                    "tipo" => "hidden",
                    "etichetta" => "Data Agg.",
                    "readonly" => true
                ),
            array(  "campo" => "scrittore",
                    "tipo" => "hidden",
                    "etichetta" => "Scrittore",
                    "readonly" => true
                ),
                array(  "campo" => "nome",
                    "tipo" => "input",
                    "etichetta" => "Nome",
                    "readonly" => false
                ),
                array(  "campo" => "codice",
                    "tipo" => "input",
                    "etichetta" => "Codice",
                    "readonly" => false
                ),
                array(  "campo" => "descrizione",
                    "tipo" => "text",
                    "etichetta" => "Descrizione",
                    "readonly" => false
                ),
            array(  "campo" => "id_prodotto",
                    "tipo" => "select2",
                    "etichetta" => "Prodotto",
                    "readonly" => false,
                    "sql" => "SELECT id AS valore, nome AS nome FROM `lista_prodotti`  WHERE LENGTH(`nome`)>1 AND  `stato` LIKE 'Attivo' ORDER BY  `nome`  ASC"
                ),
                array(  "campo" => "prezzo_sconto",
                    "tipo" => "input",
                    "etichetta" => "Prezzo Scontato",
                    "readonly" => false
                ),
                array(  "campo" => "provvigione",
                    "tipo" => "input",
                    "etichetta" => "Provvigione &euro;",
                    "readonly" => false
                ),
                array(  "campo" => "provvigione_percentuale",
                    "tipo" => "input",
                    "etichetta" => "Provvigione %",
                    "readonly" => false
                ),
                array(  "campo" => "stato",
                        "tipo" => "select_static",
                        "etichetta" => "Stato",
                        "readonly" => false,
                        "sql" => array("Attivo"=>"Attivo", "Non Attivo"=>"Non Attivo")
                    )
                )
        );

$table_listaConsuntivoVendite = array(
                "index" => array("campi" => "IF(id_calendario > 0, "
                    . "                         CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-book\"></i></a>'),
                                                IF(id_professionista > 0,
                                                    CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>'),
                                                    CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>')
                                                )
                                            ) AS 'fa-search',
                                            (SELECT CONCAT(cognome, ' ', nome) FROM lista_password WHERE lista_password.id = lista_preventivi.id_agente LIMIT 1) AS 'Commerciale',
                                            (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_preventivi.id_professionista LIMIT 1) AS 'Professionista',
                                            data_iscrizione, imponibile AS 'importo_preventivo', 
                                            (SELECT GROUP_CONCAT(lista_fatture.data_creazione SEPARATOR '<br>') FROM lista_fatture WHERE lista_fatture.id_preventivo = lista_preventivi.id) AS 'data_fattura',
                                            (SELECT GROUP_CONCAT(lista_fatture.imponibile SEPARATOR '<br>') FROM lista_fatture WHERE lista_fatture.id_preventivo = lista_preventivi.id) AS 'importo_fattura',
                                            (SELECT GROUP_CONCAT(lista_fatture.stato SEPARATOR '<br>') FROM lista_fatture WHERE lista_fatture.id_preventivo = lista_preventivi.id) AS 'stato_fattura'
                                            ",
                                "where" => " (lista_preventivi.stato LIKE 'Venduto' OR lista_preventivi.stato LIKE 'Chiuso') ".$where_lista_consuntivo_vendite,
                                "order" => "ORDER BY data_iscrizione DESC"),
                "modifica" => array(
                    array(  "campo" => "id",
                            "tipo" => "hidden",
                            "etichetta" => "ID",
                            "readonly" => true
                    ),
                    array(  "campo" => "id_professionista",
                            "tipo" => "hidden",
                            "etichetta" => "ID Professionista",
                            "readonly" => true
                    ),
                    array(  "campo" => "dataagg",
                            "tipo" => "hidden",
                            "etichetta" => "Data Agg.",
                            "readonly" => true
                    ),
                    array(  "campo" => "scrittore",
                            "tipo" => "hidden",
                            "etichetta" => "Scrittore",
                            "readonly" => true
                    )
                )
            );
?>
