<?php
/**
 * coa_action.php
 *
 * Controller for Chart of Accounts (COA) actions.
 */

require 'includes/config.php';
require 'includes/COAManager.php';

$manager = new COAManager($pdo);
$action = $_REQUEST['action'] ?? '';

try {
    if ($action === 'create') {
        $manager->createAccountHead($_POST);
        header('Location: coa.php?msg=created');
    } elseif ($action === 'update') {
        $manager->updateAccountHead($_POST['id'], $_POST);
        header('Location: coa.php?msg=updated');
    } elseif ($action === 'delete') {
        $manager->deleteAccountHead($_POST['id']);
        header('Location: coa.php?msg=deleted');
    } elseif ($action === 'get') {
        $id = $_GET['id'];
        $head = $manager->getAccountHead($id);
        header('Content-Type: application/json');
        echo json_encode($head);
        exit;
    }
} catch (Exception $e) {
    die("Error performing COA action: " . $e->getMessage());
}
