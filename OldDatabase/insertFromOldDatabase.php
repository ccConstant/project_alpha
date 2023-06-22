<?php

namespace OldDatabase;

use mysqli;

/**
 * @param $path
 * @return array
 */
function read($path): array
{
    $file = fopen($path, 'r');
    while (!feof($file)) {
        $line[] = fgetcsv($file, 1024, ';');
    }
    fclose($file);
    return $line;
}

/**
 * @param $supplier
 * @param $db
 * @return void
 */
function importSupplier($supplier, $db): void
{
    $now = getdate();
    $now = $now['year'] . '-' . $now['mon'] . '-' . $now['mday'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'];
    // Open and read the csv files
    $supplierID = read($supplier[0]);
    $suppAddress = read($supplier[1]);
    $suppContact = read($supplier[2]);
    $startAddress = 1;
    $startContact = 1;
    foreach ($supplierID as $key => $value) {
        if ($key === 0 || $key === count($supplierID) - 1) {
        } else {
            // Add the supplier to the database
            if ($value[13] === 'yes') {
                $db->query("INSERT INTO suppliers (supplr_name, supplr_receptionNumber, supplr_formID, supplr_agreementNumber, supplr_qualityCertificationNumber, supplr_specificInstructions, supplr_validate, supplr_siret, supplr_website, supplr_activity, supplr_VATNumber, supplr_endLinkToFolder, supplr_real, created_at, updated_at)
                            VALUES ('$value[0]', '$value[1]', '$value[2]', '$value[3]', '$value[4]', '$value[5]', 'validated', null, '$value[7]', '$value[8]', null, '$value[10]', '1', '$now', '$now')");
                $suppliers_id = mysqli_insert_id($db);
            } else {
                $db->query("INSERT INTO suppliers (supplr_name, supplr_receptionNumber, supplr_formID, supplr_agreementNumber, supplr_qualityCertificationNumber, supplr_specificInstructions, supplr_validate, supplr_siret, supplr_website, supplr_activity, supplr_VATNumber, supplr_endLinkToFolder, supplr_real, created_at, updated_at)
                            VALUES ('$value[0]', '$value[1]', '$value[2]', '$value[3]', '$value[4]', '$value[5]', 'validated', null, '$value[7]', '$value[8]', null, '$value[10]', '0', '$now', '$now')");
                $suppliers_id = mysqli_insert_id($db);
            }
            // Add each adress of the supplier to the database
            for ($i = $startAddress; $i < $value[11] + $startAddress; $i++) {
                if ($suppAddress[$i][4] === 'yes') {
                    $db->query("INSERT INTO supplier_adrs (supplrAdr_street, supplrAdr_town, supplrAdr_country, supplr_id, supplrAdr_validate, supplrAdr_name, supplrAdr_principal, created_at, updated_at)
                                    VALUES ('" . $suppAddress[$i][0] . "', '" . $suppAddress[$i][1] . "', '" . $suppAddress[$i][2] . "','$suppliers_id', 'validated', '" . $suppAddress[$i][3] . "', '1', '$now', '$now')");
                } else {
                    $db->query("INSERT INTO supplier_adrs (supplrAdr_street, supplrAdr_town, supplrAdr_country, supplr_id, supplrAdr_validate, supplrAdr_name, supplrAdr_principal, created_at, updated_at)
                                    VALUES ('" . $suppAddress[$i][0] . "', '" . $suppAddress[$i][1] . "', '" . $suppAddress[$i][2] . "','$suppliers_id', 'validated', '" . $suppAddress[$i][3] . "', '0', '$now', '$now')");
                }
            }
            $startAddress += $value[11];
            // Add each contact of the supplier to the database
            for ($i = $startContact; $i < $value[12] + $startContact; $i++) {
                if ($suppContact[$i][4] === 'yes') {
                    $db->query("INSERT INTO supplier_contacts (supplrContact_name, supplrContact_function, supplrContact_phoneNumber, supplrContact_email, supplr_id, supplrContact_validate, supplrContact_principal, created_at, updated_at)
                                    VALUES ('" . $suppContact[$i][0] . "', '" . $suppContact[$i][1] . "', '" . $suppContact[$i][2] . "','" . $suppContact[$i][3] . "', '$suppliers_id', 'validated', '1', '$now', '$now')");
                } else {
                    $db->query("INSERT INTO supplier_contacts (supplrContact_name, supplrContact_function, supplrContact_phoneNumber, supplrContact_email, supplr_id, supplrContact_validate, supplrContact_principal, created_at, updated_at)
                                    VALUES ('" . $suppContact[$i][0] . "', '" . $suppContact[$i][1] . "', '" . $suppContact[$i][2] . "','" . $suppContact[$i][3] . "', '$suppliers_id', 'validated', '0', '$now', '$now')");
                }
            }
            $startContact += $value[12];
        }
    }
}

/**
 * @param $comp
 * @param $db
 * @return void
 */
function importComp($comp, $db): void
{
    $now = getdate();
    $now = $now['year'] . '-' . $now['mon'] . '-' . $now['mday'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'];
    // Open and read the csv files
    $compFamily = read($comp[0]);
    $compMember = read($comp[1]);
    $compSupplier = read($comp[2]);
    $startMember = 1;
    $startSupplier = 1;
    foreach ($compFamily as $key => $value) {
        if ($key === 0 || $key === count($compFamily) - 1) {
        } else {
            $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            if (gettype($purchasedBy) !== 'array') {
                $db->query("INSERT INTO enum_purchased_bies (value) VALUES ('" . $value[2] . "')");
                $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            }
            $pb = $purchasedBy['id'];
            // Add the family to the database
            $db->query("INSERT INTO comp_families (compFam_ref, compFam_design, enumPurchasedBy_id, compFam_drawingPath, compFam_version, compFam_variablesCharac, compFam_variablesCharacDesign, compFam_genRef, compFam_genDesign, compFam_validate, created_at, updated_at)
                            VALUES ('" . $value[0] . "', '" . $value[1] . "', '" . $pb . "', '" . $value[3] . "', '" . $value[4] . "', '" . $value[5] . "', null, '" . $value[6] . "', '" . $value[7] . "', 'validated', '$now', '$now')");
            $compFamilyId = mysqli_insert_id($db);
                // Add the criticality to the database
            $db->query("INSERT INTO criticalities (crit_artCriticality, crit_artMaterialContactCriticality, crit_artMaterialFunctionCriticality, crit_artProcessCriticality, crit_validate, crit_remarks, compFam_id,crit_justification, created_at, updated_at)
                            VALUES ('" . $value[8] . "', '" . $value[9] . "', '" . $value[10] . "', '" . $value[8] . "', 'validated', null, '" . $compFamilyId . "', null, '$now', '$now')");
            // Add each member of the family to the database
            for ($i = $startMember; $i < $value[11] + $startMember; $i++) {
                if ($compMember[$i][1] === 'yes') {
                    $db->query("INSERT INTO comp_family_members (compMb_dimension, compMb_design, compMb_sameValues, compFam_id, created_at, updated_at)
                                    VALUES ('" . $compMember[$i][0] . "', null, '1', '" . $compFamilyId . "', '$now', '$now')");
                } else {
                    $db->query("INSERT INTO comp_family_members (compMb_dimension, compMb_design, compMb_sameValues, compFam_id, created_at, updated_at)
                                    VALUES ('" . $compMember[$i][0] . "', '" . $compMember[$i][2] . "', '0', '" . $compFamilyId . "', '$now', '$now')");
                }
            }
            $startMember += $value[11];
            // Add each supplier of the family to the database
            for ($i = $startSupplier; $i < $value[12] + $startSupplier; $i++) {
                if ($compSupplier[$i][0] === 'Alpha') {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, compFam_id, created_at, updated_at)
                                VALUES (null, 'validated', '" . $compFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $compSupplier[$i][0] . "'")->fetch_assoc();
                    $db->query("INSERT INTO pivot_comp_fam_supplr (compFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $compFamilyId . "', '" . $supplierId['id'] . "', null, LAST_INSERT_ID(), '$now', '$now')");
                } else {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, compFam_id, created_at, updated_at)
                                VALUES ('" . $compSupplier[$i][2] . "', 'validated', '" . $compFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $compSupplier[$i][0] . "'")->fetch_assoc();
                    $db->query("INSERT INTO pivot_comp_fam_supplr (compFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $compFamilyId . "', '" . $supplierId['id'] . "', '" . $compSupplier[$i][1] . "', LAST_INSERT_ID(), '$now', '$now')");
                }
            }
            $startSupplier += $value[12];
        }
    }
}

/**
 * @param $cons
 * @param $db
 * @return void
 */
function importCons($cons, $db): void
{
    $now = getdate();
    $now = $now['year'] . '-' . $now['mon'] . '-' . $now['mday'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'];
    // Open and read the csv files
    $consFamily = read($cons[0]);
    $consMember = read($cons[1]);
    $consSupplier = read($cons[2]);
    $startMember = 1;
    $startSupplier = 1;
    foreach ($consFamily as $key => $value) {
        if ($key === 0 || $key === count($consFamily) - 1) {
        } else {
            $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            if (gettype($purchasedBy) !== 'array') {
                $db->query("INSERT INTO enum_purchased_bies (value) VALUES ('" . $value[2] . "')");
                $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            }
            $pb = $purchasedBy['id'];
            // Add the family to the database
            $db->query("INSERT INTO cons_families (consFam_ref, consFam_design, enumPurchasedBy_id, consFam_drawingPath, consFam_version, consFam_variablesCharac, consFam_variablesCharacDesign, consFam_genRef, consFam_genDesign, consFam_validate, created_at, updated_at)
                            VALUES ('" . $value[0] . "', '" . $value[1] . "', '" . $pb . "', '" . $value[3] . "', '" . $value[4] . "', '" . $value[5] . "', null, '" . $value[7] . "', '" . $value[8] . "', 'validated', '$now', '$now')");
            $consFamilyId = mysqli_insert_id($db);
            // Add the criticality to the database
            $db->query("INSERT INTO criticalities (crit_artCriticality, crit_artMaterialContactCriticality, crit_artMaterialFunctionCriticality, crit_artProcessCriticality, crit_validate, crit_remarks, consFam_id,crit_justification, created_at, updated_at)
                            VALUES ('" . $value[9] . "', '" . $value[10] . "', '" . $value[11] . "', '" . $value[9] . "', 'validated', null, '" . $consFamilyId . "', null, '$now', '$now')");
            // Add each member of the family to the database
            for ($i = $startMember; $i < $value[11] + $startMember; $i++) {
                if ($consMember[$i][1] === 'yes') {
                    $db->query("INSERT INTO cons_family_members (consMb_dimension, consMb_design, consMb_sameValues, consFam_id, created_at, updated_at)
                                    VALUES ('" . $consMember[$i][0] . "', null, '1', '" . $consFamilyId . "', '$now', '$now')");
                } else {
                    $db->query("INSERT INTO cons_family_members (consMb_dimension, consMb_design, consMb_sameValues, consFam_id, created_at, updated_at)
                                    VALUES ('" . $consMember[$i][0] . "', '" . $consMember[$i][2] . "', '0', '" . $consFamilyId . "', '$now', '$now')");
                }
            }
            $startMember += $value[11];
            // Add each supplier of the family to the database
            for ($i = $startSupplier; $i < $value[12] + $startSupplier; $i++) {
                if ($consSupplier[$i][0] === 'Alpha') {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, consFam_id, created_at, updated_at)
                                VALUES (null, 'validated', '" . $consFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $consSupplier[$i][0] . "'")->fetch_assoc();
                    $db->query("INSERT INTO pivot_cons_fam_supplr (consFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $consFamilyId . "', '" . $supplierId['id'] . "', null, LAST_INSERT_ID(), '$now', '$now')");
                } else {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, consFam_id, created_at, updated_at)
                                VALUES ('" . $consSupplier[$i][2] . "', 'validated', '" . $consFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $consSupplier[$i][0] . "'")->fetch_assoc();
                    print_r($supplierId);
                    print_r($i);
                    $db->query("INSERT INTO pivot_cons_fam_supplr (consFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $consFamilyId . "', '" . $supplierId['id'] . "', '" . $consSupplier[$i][1] . "', LAST_INSERT_ID(), '$now', '$now')");
                }
            }
            $startSupplier += $value[12];
        }
    }
}

/**
 * @param $raw
 * @param $db
 * @return void
 */
function importRaw($raw, $db): void
{
    $now = getdate();
    $now = $now['year'] . '-' . $now['mon'] . '-' . $now['mday'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'];
    // Open and read the csv files
    $rawFamily = read($raw[0]);
    $rawMember = read($raw[1]);
    $rawSupplier = read($raw[2]);
    $startMember = 1;
    $startSupplier = 1;
    foreach ($rawFamily as $key => $value) {
        if ($key === 0 || $key === count($rawFamily) - 1) {
        } else {
            $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            if (gettype($purchasedBy) !== 'array') {
                $db->query("INSERT INTO enum_purchased_bies (value) VALUES ('" . $value[2] . "')");
                $purchasedBy = $db->query("SELECT id FROM enum_purchased_bies WHERE value = '" . $value[2] . "'")->fetch_assoc();
            }
            $pb = $purchasedBy['id'];
            // Add the family to the database
            $db->query("INSERT INTO raw_families (rawFam_ref, rawFam_design, enumPurchasedBy_id, rawFam_drawingPath, rawFam_variablesCharac, rawFam_variablesCharacDesign, rawFam_genRef, rawFam_genDesign, rawFam_validate, created_at, updated_at)
                            VALUES ('" . $value[0] . "', '" . $value[1] . "', '" . $pb . "', '" . $value[3] . "', '" . $value[4] . "', null, '" . $value[5] . "', '" . $value[6] . "', 'validated', '$now', '$now')");
            $rawFamilyId = mysqli_insert_id($db);
            // Add the criticality to the database
            $db->query("INSERT INTO criticalities (crit_artCriticality, crit_artMaterialContactCriticality, crit_artMaterialFunctionCriticality, crit_artProcessCriticality, crit_validate, crit_remarks, rawFam_id,crit_justification, created_at, updated_at)
                            VALUES ('" . $value[7] . "', '" . $value[8] . "', '" . $value[9] . "', '" . $value[7] . "', 'validated', null, '" . $rawFamilyId . "', null, '$now', '$now')");
            // Add each member of the family to the database
            for ($i = $startMember; $i < $value[10] + $startMember; $i++) {
                if ($rawMember[$i][1] === 'yes') {
                    $db->query("INSERT INTO raw_family_members (rawMb_dimension, rawMb_design, rawMb_sameValues, rawFam_id, created_at, updated_at)
                                    VALUES ('" . $rawMember[$i][0] . "', null, '1', '" . $rawFamilyId . "', '$now', '$now')");
                } else {
                    $db->query("INSERT INTO raw_family_members (rawMb_dimension, rawMb_design, rawMb_sameValues, rawFam_id, created_at, updated_at)
                                    VALUES ('" . $rawMember[$i][0] . "', '" . $rawMember[$i][2] . "', '0', '" . $rawFamilyId . "', '$now', '$now')");
                }
            }
            $startMember += $value[10];
            // Add each supplier of the family to the database
            for ($i = $startSupplier; $i < $value[11] + $startSupplier; $i++) {
                if ($rawSupplier[$i][0] === 'Alpha') {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, rawFam_id, created_at, updated_at)
                                VALUES (null, 'validated', '" . $rawFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $rawSupplier[$i][0] . "'")->fetch_assoc();
                    $db->query("INSERT INTO pivot_raw_fam_supplr (rawFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $rawFamilyId . "', '" . $supplierId['id'] . "', null, LAST_INSERT_ID(), '$now', '$now')");
                } else {
                    $db->query("INSERT INTO purchase_specifications (purSpe_requiredDoc, purSpe_validate, rawFam_id, created_at, updated_at)
                                VALUES ('" . $rawSupplier[$i][2] . "', 'validated', '" . $rawFamilyId . "', '$now', '$now')");
                    $supplierId = $db->query("SELECT id FROM suppliers WHERE supplr_name = '" . $rawSupplier[$i][0] . "'")->fetch_assoc();
                    $db->query("INSERT INTO pivot_raw_fam_supplr (rawFam_id, supplr_id, supplr_ref, purSpec_id, created_at, updated_at)
                            VALUES ('" . $rawFamilyId . "', '" . $supplierId['id'] . "', '" . $rawSupplier[$i][1] . "', LAST_INSERT_ID(), '$now', '$now')");
                }
            }
            $startSupplier += $value[11];
        }
    }
}

// Connect to the alpha_app database
$db = new mysqli('localhost', 'root', '', 'alpha_project', 3306);
// Set the different csv files, ordered by groups
// The first group is the suppliers, because they are needed for the differents articles
$supplier = [
    'supplier.csv',
    'supplierAddress.csv',
    'supplierContact.csv'
];
importSupplier($supplier, $db);
$comp = [
    'compFamily.csv',
    'compMember.csv',
    'compSupplier.csv'
];
importComp($comp, $db);
$cons = [
    'consFamily.csv',
    'consMember.csv',
    'consSupplier.csv'
];
importCons($cons, $db);
$raw = [
    'rawFamily.csv',
    'rawMember.csv',
    'rawSupplier.csv'
];
importRaw($raw, $db);
?>
