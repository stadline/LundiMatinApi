<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/7/16
 * Time: 3:48 PM
 */

namespace Stadline\LundiMatinClientBundle\Mock;


class SoapServiceMock implements SoapServiceMockInterface {

    public function getFacturesByRefClient($refClient)
    {
        if($refClient == 'C-000000-00001'){
            // type_doc = 3
            $array = array(
                        array( 'type_doc' => '3',
                            'etat_doc' => '15',
                            'ref_doc' => 'BLC-00150',
                            'ref_doc_externe' => '',
                            'code_affaire' => NULL,
                            'date_creation_doc' => '2014-12-02 14:26:20',
                            'ref_contact' => 'C-000000-00001',
                            'nom_contact' => 'PI MOTION',
                            'ref_adr_contact' => NULL
                        )
            );
            return array_values($array);
        }
        else if ($refClient == 'C-000000-00002'){
            // type_doc = 4
            $array = array(
                       array( 'type_doc' => '4',
                            'etat_doc' => '15',
                            'ref_doc' => 'BLC-00150',
                            'ref_doc_externe' => '',
                            'code_affaire' => NULL,
                            'date_creation_doc' => '2014-12-02 14:26:20',
                            'ref_contact' => 'C-000000-00002',
                            'nom_contact' => 'PI MOTION',
                            'ref_adr_contact' => NULL
                       )
            );
            return array_values($array);
        }
        else if ($refClient == 'C-000000-00003'){
                // type_doc = 4
                $array = array(
                    array( 'type_doc' => '4',
                            'etat_doc' => '15',
                            'ref_doc' => 'BLC-00150',
                            'ref_doc_externe' => '',
                            'code_affaire' => NULL,
                            'date_creation_doc' => '2014-12-02 14:26:20',
                            'ref_contact' => 'C-000000-00003',
                            'nom_contact' => 'PI MOTION',
                            'ref_adr_contact' => NULL
                        ),
                    array( 'type_doc' => '4',
                        'etat_doc' => '15',
                        'ref_doc' => 'BLC-00160',
                        'ref_doc_externe' => '',
                        'code_affaire' => NULL,
                        'date_creation_doc' => '2014-12-02 14:26:20',
                        'ref_contact' => 'C-000000-00003',
                        'nom_contact' => 'PI MOTION',
                        'ref_adr_contact' => NULL
                    )
                );
                return array_values($array);
        }
        else if ($refClient == 'C-000000-00004'){
            // type_doc = 4
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '15',
                    'ref_doc' => 'BLC-00170',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00004',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        else if ($refClient == 'C-000000-00005'){
            // type_doc = 4
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '15',
                    'ref_doc' => 'BLC-00180',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00005',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        else {
            return null;
        }
    }

    public function getFacturesByRef($refDoc)
    {
        // etat_doc = 16
        if ($refDoc == "BLC-00004") {
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '16',
                    'ref_doc' => 'BLC-00004',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00001',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        // etat doc = 17
        else if ($refDoc == "BLC-00150") {
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '17',
                    'ref_doc' => 'BLC-00150',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00002',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        // etat doc = 18
        else if ($refDoc == "BLC-00170") {
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '18',
                    'ref_doc' => 'BLC-00170',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00004',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        // etat doc = 19
        else if ($refDoc == "BLC-00180") {
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '19',
                    'ref_doc' => 'BLC-00180',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00005',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
        // not in switch
        else {
            $array = array(
                array( 'type_doc' => '4',
                    'etat_doc' => '15',
                    'ref_doc' => 'BLC-00005',
                    'ref_doc_externe' => '',
                    'code_affaire' => NULL,
                    'date_creation_doc' => '2014-12-02 14:26:20',
                    'ref_contact' => 'C-000000-00001',
                    'nom_contact' => 'PI MOTION',
                    'ref_adr_contact' => NULL
                )
            );
            return array_values($array);
        }
    }

    public function getDocument($refDoc)
    {
        if($refDoc == "BLC-00004" )
        {
            $array = array( 'id_doc_line' => '379',
                'ref_doc_line' => 'RDL-000000-000aj',
                'ref_doc' => 'BLC-00004',
                'id_doc_line_parent' => NULL,
                'ref_doc_line_parent' => NULL,
                'ordre' => '1',
                'visible' => '1',
                'ref_article' => 'A-000000-00115',
                'lib_article' => 'Renouvellement annuel de vos noms de Domaine',
                'desc_article' => '',
                'ref_suivi_client' => '',
                'qte' => '1',
                'remise_mode' => 'pourcent',
                'remise' => '0',
                'id_tva' => '71',
                'tva' => '20',
                'pu_ht' => '76.89',
                'pu_ttc' => '92.268',
                'net_ht' => '100',
                'montant_tva' => '100',
                'montant_ttc' => '200',
                'nb_decimales' => '2',
                'pa_ht' => '0',
                'pa_forced' => '1',
                'fd_ht' => '0',
                'fd_forced' => '0',
                'ref_art_categ' => 'A.C-000000-0000a',
                'id_modele_spe' => NULL,
                'id_valo' => '3',
                'valo_indice' => '1',
                'ref_oem' => '',
                'ref_interne' => NULL,
                'modele' => 'materiel',
                'lot' => '0',
                'fifo' => '0',
                'gestion_sn' => '0',
                'gestion_sn_auto' => '0',
                'gestion_sn_cachee' => '0',
                'gestion_sn_liaison_mode' => NULL,
                'gestion_sn_liaison_mode_type' => NULL,
                'abrev_valo' => 'Kg.',
                'id_doc_line_cdc' => NULL,
                'stock' => '-1',
                'ref_stock_article' => 'SKA-000000-0000b',
                'type_of_line' => 'article'
            );
            return $array;
        }
        else if ($refDoc == "BLC-00150") {
            $array = array( 'id_doc_line' => '380',
                'ref_doc_line' => 'RDL-000000-000aj',
                'ref_doc' => 'BLC-00150',
                'id_doc_line_parent' => NULL,
                'ref_doc_line_parent' => NULL,
                'ordre' => '1',
                'visible' => '1',
                'ref_article' => 'A-000000-00115',
                'lib_article' => 'Renouvellement annuel de vos noms de Domaine',
                'desc_article' => '',
                'ref_suivi_client' => '',
                'qte' => '1',
                'remise_mode' => 'pourcent',
                'remise' => '0',
                'id_tva' => '71',
                'tva' => '20',
                'pu_ht' => '76.89',
                'pu_ttc' => '92.268',
                'net_ht' => '100',
                'montant_tva' => '100',
                'montant_ttc' => '200',
                'nb_decimales' => '2',
                'pa_ht' => '0',
                'pa_forced' => '1',
                'fd_ht' => '0',
                'fd_forced' => '0',
                'ref_art_categ' => 'A.C-000000-0000a',
                'id_modele_spe' => NULL,
                'id_valo' => '3',
                'valo_indice' => '1',
                'ref_oem' => '',
                'ref_interne' => NULL,
                'modele' => 'materiel',
                'lot' => '0',
                'fifo' => '0',
                'gestion_sn' => '0',
                'gestion_sn_auto' => '0',
                'gestion_sn_cachee' => '0',
                'gestion_sn_liaison_mode' => NULL,
                'gestion_sn_liaison_mode_type' => NULL,
                'abrev_valo' => 'Kg.',
                'id_doc_line_cdc' => NULL,
                'stock' => '-1',
                'ref_stock_article' => 'SKA-000000-0000b',
                'type_of_line' => 'article'
            );
            return $array;
        }

        else if ($refDoc == "BLC-00160") {
            $array = array( 'id_doc_line' => '381',
                'ref_doc_line' => 'RDL-000000-000aj',
                'ref_doc' => 'BLC-00006',
                'id_doc_line_parent' => NULL,
                'ref_doc_line_parent' => NULL,
                'ordre' => '1',
                'visible' => '1',
                'ref_article' => 'A-000000-00115',
                'lib_article' => 'Renouvellement annuel de vos noms de Domaine',
                'desc_article' => '',
                'ref_suivi_client' => '',
                'qte' => '1',
                'remise_mode' => 'pourcent',
                'remise' => '0',
                'id_tva' => '71',
                'tva' => '20',
                'pu_ht' => '50.89',
                'pu_ttc' => '92.268',
                'net_ht' => '100',
                'montant_tva' => '100',
                'montant_ttc' => '200',
                'nb_decimales' => '2',
                'pa_ht' => '0',
                'pa_forced' => '1',
                'fd_ht' => '0',
                'fd_forced' => '0',
                'ref_art_categ' => 'A.C-000000-0000a',
                'id_modele_spe' => NULL,
                'id_valo' => '3',
                'valo_indice' => '1',
                'ref_oem' => '',
                'ref_interne' => NULL,
                'modele' => 'materiel',
                'lot' => '0',
                'fifo' => '0',
                'gestion_sn' => '0',
                'gestion_sn_auto' => '0',
                'gestion_sn_cachee' => '0',
                'gestion_sn_liaison_mode' => NULL,
                'gestion_sn_liaison_mode_type' => NULL,
                'abrev_valo' => 'Kg.',
                'id_doc_line_cdc' => NULL,
                'stock' => '-1',
                'ref_stock_article' => 'SKA-000000-0000b',
                'type_of_line' => 'article'
            );
            return $array;
        }
        else if($refDoc == "BLC-00170") {
            $array = array( 'id_doc_line' => '382',
                'ref_doc_line' => 'RDL-000000-000aj',
                'ref_doc' => 'BLC-00004',
                'id_doc_line_parent' => NULL,
                'ref_doc_line_parent' => NULL,
                'ordre' => '1',
                'visible' => '1',
                'ref_article' => 'A-000000-00115',
                'lib_article' => 'Renouvellement annuel de vos noms de Domaine',
                'desc_article' => '',
                'ref_suivi_client' => '',
                'qte' => '1',
                'remise_mode' => 'pourcent',
                'remise' => '0',
                'id_tva' => '71',
                'tva' => '20',
                'pu_ht' => '76.89',
                'pu_ttc' => '92.268',
                'net_ht' => '100',
                'montant_tva' => '50',
                'montant_ttc' => '200',
                'nb_decimales' => '2',
                'pa_ht' => '0',
                'pa_forced' => '1',
                'fd_ht' => '0',
                'fd_forced' => '0',
                'ref_art_categ' => 'A.C-000000-0000a',
                'id_modele_spe' => NULL,
                'id_valo' => '3',
                'valo_indice' => '1',
                'ref_oem' => '',
                'ref_interne' => NULL,
                'modele' => 'materiel',
                'lot' => '0',
                'fifo' => '0',
                'gestion_sn' => '0',
                'gestion_sn_auto' => '0',
                'gestion_sn_cachee' => '0',
                'gestion_sn_liaison_mode' => NULL,
                'gestion_sn_liaison_mode_type' => NULL,
                'abrev_valo' => 'Kg.',
                'id_doc_line_cdc' => NULL,
                'stock' => '-1',
                'ref_stock_article' => 'SKA-000000-0000b',
                'type_of_line' => 'article'
            );
            return $array;
        }
        else if($refDoc == "BLC-00180") {
            $array = array( 'id_doc_line' => '383',
                'ref_doc_line' => 'RDL-000000-000aj',
                'ref_doc' => 'BLC-00004',
                'id_doc_line_parent' => NULL,
                'ref_doc_line_parent' => NULL,
                'ordre' => '1',
                'visible' => '1',
                'ref_article' => 'A-000000-00115',
                'lib_article' => 'Renouvellement annuel de vos noms de Domaine',
                'desc_article' => '',
                'ref_suivi_client' => '',
                'qte' => '1',
                'remise_mode' => 'pourcent',
                'remise' => '0',
                'id_tva' => '71',
                'tva' => '20',
                'pu_ht' => '76.89',
                'pu_ttc' => '92.268',
                'net_ht' => '100',
                'montant_tva' => '100',
                'montant_ttc' => '200',
                'nb_decimales' => '2',
                'pa_ht' => '0',
                'pa_forced' => '1',
                'fd_ht' => '0',
                'fd_forced' => '0',
                'ref_art_categ' => 'A.C-000000-0000a',
                'id_modele_spe' => NULL,
                'id_valo' => '3',
                'valo_indice' => '1',
                'ref_oem' => '',
                'ref_interne' => NULL,
                'modele' => 'materiel',
                'lot' => '0',
                'fifo' => '0',
                'gestion_sn' => '0',
                'gestion_sn_auto' => '0',
                'gestion_sn_cachee' => '0',
                'gestion_sn_liaison_mode' => NULL,
                'gestion_sn_liaison_mode_type' => NULL,
                'abrev_valo' => 'Kg.',
                'id_doc_line_cdc' => NULL,
                'stock' => '-1',
                'ref_stock_article' => 'SKA-000000-0000b',
                'type_of_line' => 'article'
            );
            return $array;
        }
        else {
            return array(null);
        }
    }
}