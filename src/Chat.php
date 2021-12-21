<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    /**
     *  $clients - to store client 
     */
    protected $clients;

    /**
     * $data - to store session data
     */
    protected $data;

    public function __construct() {

        $this->clients = new \SplObjectStorage;
        $this->loadData();
        
    }

    /**
     * 
     * lodaData: load data from file
     */
    public function loadData(){
        try{

            $this->data = json_decode(file_get_contents(__DIR__.'/data.json'));;
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * saveData: to save the updated to to file
     */
    public function saveData(){
        try{

            file_put_contents(__DIR__.'/data.json',json_encode($this->data));

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    // storing  the new connections to send msg later
    public function onOpen(ConnectionInterface $conn) {

        $this->clients->attach($conn);

        // sending session data to client
        $conn->send(json_encode($this->data));
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        // decoding the json message
        $decodedMsg = json_decode($msg);

        // storing the updated index
        $index = $decodedMsg->index;

        // delete 'index' property from the message
        unset($decodedMsg->index);

        // store updated data to the index
        $this->data->sessionData[$index] = $decodedMsg;

        // saved the updated data to file
        $this->saveData();

        // encode the the updated data and send to every connection
        $newData = json_encode($this->data);
        foreach($this->clients as $client){
            $client->send($newData);
        }
    }

    public function onClose(ConnectionInterface $conn) {

        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

?>