<?php

namespace MyApp;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later

        $this->clients->attach($conn);

        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryarray);
        $token = $queryarray['token'];

        $connDB = new \mysqli('localhost', 'root', '', 'pami');
        if ($connDB->connect_error) {
            die("Connection failed: " . $connDB->connect_error);
        } else {
            $con = $conn->resourceId;
            $sql = "UPDATE users
        SET `conn_id` = '$con'
        WHERE  `token` = '$token'";
            $connDB->query($sql);
            $connDB->close();

            echo "New connection! ({$conn->resourceId})\n";
            echo "New connection! ({$querystring})";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );

        $msg = json_decode($msg);

        $adminid = isset($msg->adminid) ? $msg->adminid : '';
        $userid = isset($msg->userid) ? $msg->userid : '';
        $roomid = isset($msg->roomid) ? $msg->roomid : '';

        $accept_to_user = isset($msg->accept_to_user) ? $msg->accept_to_user : '';
        $decline_to_user = isset($msg->decline_to_user) ? $msg->decline_to_user : '';

        $join = isset($msg->join) ? $msg->join : null;
        $cancel = isset($msg->cancel) ? $msg->cancel : null;

        $save_message = isset($msg->save_message) ? $msg->save_message : '';
        $file_type = isset($msg->file_type) ? $msg->file_type : '';
        $file_type = isset($msg->file_type) ? $msg->file_type : '';

        $userid_type = isset($msg->userid_type) ? $msg->userid_type : '';
        $roomid_type = isset($msg->roomid_type) ? $msg->roomid_type : '';
        $username_type = isset($msg->username_type) ? $msg->username_type : '';
        $userval_type = isset($msg->userval_type) ? $msg->userval_type : '';

        $seen = isset($msg->seen) ? $msg->seen : '';
        $focusSeen = isset($msg->focusSeen) ? $msg->focusSeen : '';
        $bann = isset($msg->bann) ? $msg->bann : '';
        $banned = isset($msg->banned) ? $msg->banned : '';
        $removed = isset($msg->removed) ? $msg->removed : '';

        ///////////////////////////////////////////////////////join///////////////////////////////////////////////////////////////////
        if (isset($join) && $join !== null) {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$adminid'";
                $result = $connDB->query($sql);
                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode($msg->join));
                }
            }
        }
        ///////////////////////////////////////////////////////cancel///////////////////////////////////////////////////////////////////
        if (isset($cancel) && $cancel !== null) {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
            from users
            WHERE  id = '$adminid'";
                $result = $connDB->query($sql);
                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode($msg->cancel));
                }
            }
        }
        ////////////////////////////////////////////////////accept_to_user//////////////////////////////////////////////////////////////
        if (isset($accept_to_user) && $accept_to_user == "accept_to_user") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$userid'";
                $result = $connDB->query($sql);

                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(["accept_to_user" => $msg->accept_to_user, "data" => $msg]));
                }
            }
        }
        ////////////////////////////////////////////////////decline_to_user//////////////////////////////////////////////////////////////
        if (isset($decline_to_user) && $decline_to_user == "decline_to_user") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$userid'";
                $result = $connDB->query($sql);

                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(["decline_to_user" => $msg->decline_to_user, "data" => $msg]));
                }
            }
        }

        ///////////////////////////////////////////////////////save_message///////////////////////////////////////////////////////////////
        if (isset($save_message) && $save_message == "save_message") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {

                $sql = "SELECT
                            conn_id
                        FROM
                            users
                        JOIN users_rooms
                        ON users.id = users_rooms.user_id
                        where users_rooms.room_id = $roomid";
                $result = $connDB->query($sql);
                $to_clients = $result->fetch_all();

                $sqlroom = "SELECT
                            `rooms`.`name`
                        FROM
                            `rooms`
                        where `rooms`.`id` = $roomid ";
                $result = $connDB->query($sqlroom);
                $roomname = $result->fetch_assoc()["name"];


                $sqlI = " UPDATE `users_rooms`
                    SET `users_rooms`.`room_count` = `room_count` +1
                    WHERE `users_rooms`.`user_id` <> $userid AND `users_rooms`.`room_id` = $roomid and `users_rooms`.`rank` <> 0";

                $connDB->query($sqlI);


                $sqlcount = "SELECT
                    `room_count` ,`user_id`
                FROM
                    `users_rooms`
                WHERE
                    `user_id` <> $userid AND `room_id` = $roomid";
                $sqlcount = $connDB->query($sqlcount);
                $sqlcount = $sqlcount->fetch_all();

                $connDB->close();
            }
            for ($i = 0; $i < count($to_clients); $i++) {
                foreach ($this->clients as $client) {
                    if ((int) $to_clients[$i][0] == $client->resourceId) {
                        // The sender is not the receiver, send to each client connected
                        if (isset($file_type)) {
                                $client->send(json_encode(["save_message" => $msg, "file" => $file_type, 'room_name' => $roomname, 'room_count' => $sqlcount]));
                        } else {
                                $client->send(json_encode(["save_message" => $msg, 'room_name' => $roomname, 'room_count' => $sqlcount]));
                            
                        }
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////is_typing///////////////////////////////////////////////////////////////
        if (isset($username_type) && $username_type != '') {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {

                $sql = "SELECT
                            `users`.`conn_id`
                        FROM
                            users
                        JOIN users_rooms
                        ON users.id = users_rooms.user_id
                        where users_rooms.room_id = $roomid_type and users_rooms.user_id <> $userid_type";
                $result = $connDB->query($sql);
                $to_clients = $result->fetch_all();
                $connDB->close();
            }
            for ($i = 0; $i < count($to_clients); $i++) {
                foreach ($this->clients as $client) {
                    if ($to_clients[$i][0] == $client->resourceId) {
                        // if ($from !== $client->resourceId) {
                        // The sender is not the receiver, send to each client connected

                        $client->send(json_encode(["is_typing" => $username_type, "userval" => $userval_type, 'roomval_type' => $roomid_type]));

                        // }
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////focusSeen///////////////////////////////////////////////////////////////
        if (isset($focusSeen) && $focusSeen != '') {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {

                $sql = "SELECT
                            `users`.`conn_id`
                        FROM
                            users
                        JOIN users_rooms
                        ON users.id = users_rooms.user_id
                        where users_rooms.room_id = $roomid";
                $result = $connDB->query($sql);
                $to_clients = $result->fetch_all();
                $connDB->close();
            }
            for ($i = 0; $i < count($to_clients); $i++) {
                foreach ($this->clients as $client) {
                    if ($to_clients[$i][0] == $client->resourceId) {
                        // if ($from !== $client->resourceId) {
                        // The sender is not the receiver, send to each client connected

                        $client->send(json_encode(["roomid" => $roomid, "focusSeen" => 'focusSeen']));

                        // }

                    }
                }
            }
        }
        ////////////////////////////////////////////////////-----bann----//////////////////////////////////////////////////////////////
        if (isset($bann) && $bann == "bann") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$userid'";
                $result = $connDB->query($sql);

                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(["roomid" => $msg->roomid,"bann" => $msg->bann]));
                }
            }
        }
        ////////////////////////////////////////////////////-----banned----//////////////////////////////////////////////////////////////
        if (isset($banned) && $banned == "banned") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$userid'";
                $result = $connDB->query($sql);

                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(["roomid" => $msg->roomid,"banned" => $msg->banned]));
                }
            }
        }
        ////////////////////////////////////////////////////-----removed----//////////////////////////////////////////////////////////////
        if (isset($removed) && $removed == "removed") {
            $connDB = new \mysqli('localhost', 'root', '', 'pami');
            if ($connDB->connect_error) {
                die("Connection failed: " . $connDB->connect_error);
            } else {
                $sql = "SELECT conn_id
                from users
                WHERE  id = '$userid'";
                $result = $connDB->query($sql);

                $to_client = $result->fetch_assoc()["conn_id"];

                $connDB->close();
            }
            foreach ($this->clients as $client) {
                if ($to_client == $client->resourceId) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(["roomid"=>$roomid,"removed" => $msg->removed]));
                }
            }
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
