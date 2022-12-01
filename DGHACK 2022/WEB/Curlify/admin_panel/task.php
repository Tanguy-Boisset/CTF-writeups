<?php

include_once "users.php";

session_start();

function handle_task_creation()
{
    if (isset($_POST) && !empty($_POST)) {

        $required_elements = array("author", "type", "assignee", "description");

        foreach ($required_elements as &$el) {
            if (!isset($_POST[$el]) || empty($_POST[$el]))
                return '<div class="notification is-danger">Missing value ' . $el . '.</div>';
        }

        $ticket_id = strval(random_int(0, 9999));
        $dt = new DateTime();
        $date = $dt->format('Y-m-d H:i:s');

        $filename = "task_$ticket_id.txt";
        $fp = fopen(__DIR__ . "/tasks/" . $filename, "w+");

        $users = new User();
        $list_username = $users->get_all_username();
        $list_type = array("incident", "bug", "vulnerability", "request");

        if (!in_array($_POST["author"], $list_username) || !in_array($_POST["type"], $list_type) || !in_array($_POST["assignee"], $list_username))
            return '<div class="notification is-danger">Invalid data</div>';
        else {
            $content = "=== Ticket N° $ticket_id ===\n";
            $content .= "Creation Date: $date\n";
            if ($_SESSION["userid"]) {
                $content .= "UserId: " . $_SESSION["userid"] . "\n";
                if ($_SESSION["user_prefs"]) $content .= "Preferences: " . $_SESSION["user_prefs"] . "\n";
            }
            $content .= "Author: " . $_POST["author"] . "\n";
            $content .= "Assignee: " . $_POST["assignee"] . "\n";
            $content .= "Description: " . $_POST["description"] . "\n";
        }

        fwrite($fp, $content);
        fclose($fp);

        return '
                <div class="notification is-success">Ticket with id ' . $ticket_id . ' created</div>
                <div class="notification is-info">Your ticket will be treated and removed in a few seconds (estimated time: ~5 seconds)</div>
            ';

    }

}


?>

<!DOCTYPE html>
<html>

<link rel="stylesheet" href="../css/bulma.min.css">

<section class="section container" >
    <div class="columns">
        <div class="column">
            <navbar class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-menu">
                    <div class="navbar-start">
                        <div class="navbar-item">
                            <a href="index.php">Home</a>
                        </div>
                        <div class="navbar-item">
                            <a href="task.php">Tasks</a>
                        </div>
                        <div class="navbar-item">
                            <a href="create_task.php">Tasks creation</a>
                        </div>
                    </div>
                </div>
            </navbar>

            <?php
                if (!$_SESSION["isConnected"] || $_SESSION["username"] !== "admin")
                    die('<div class="notification is-danger">Not authorized</div>');
            ?>
            <div class="container">
                <h1 class="title has-text-centered">Task Management Panel</h1>
                <figure>
                    <form method="post" action="#">
                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <label class="label">Author:</label>
                                <input name="author" class="input" type="text" placeholder="author">
                            </p>
                        </div>

                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <label class="label">Type:</label>
                                <input name="type" class="input" type="text" placeholder="type">
                            </p>
                        </div>

                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <label class="label">Assignee:</label>
                                <input name="assignee" class="input" type="text" placeholder="assignee">
                            </p>
                        </div>

                        <div class="field">
                            <p class="control has-icons-left has-icons-right">
                                <label class="label">Description:</label>
                                <input name="description" class="input" type="text" placeholder="description">
                            </p>
                        </div>

                        <div class="field">
                            <p class="control">
                            <div class="has-text-centered">
                                <button class="button is-link has-text-centered">
                                    Create
                                </button>
                            </div>
                            </p>
                        </div>
                    </form>
                </figure>
            </div>

            <?php
            echo '<div class="container">
                        <figure>
                            '.handle_task_creation().'
                        </figure>
                    </div>';
            ?>
        </div>
    </div>
</html>
