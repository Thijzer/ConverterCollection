<?php

namespace Commands\TransUpDown\Component\Reader;

// implements FileManager \ FilerReader
class TransmissionFileReader
{
    private $transmission;
    private $list = array();

    public function __construct()
    {
        $config = include ('config.php');
        $user = $config['user'];
        $pass = $config['pass'];
        $this->transmission = "transmission-remote -N ~/.netrc.transmission --auth=$user:$pass";
        exec($this->transmission." --list | sed -e '1d' -e '\$d' -e '/^$/d' | awk '{print $1}'", $list);

        // broken transmission files are marked with a star string on the id
        foreach ($list as $id) {
            $id = str_replace('*', '', $id);
            $this->list[$id] = $this->read($id);
        }
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->list;
    }

    private function read($id)
    {
        exec($this->transmission.' -t '.$id.' --info', $info);

        return $info;
    }

    public function sleep()
    {
        exec($this->transmission.' -t all --stop');
    }

    public function wake($id)
    {
        exec($this->transmission.' -t '.$id.' --start');
    }

    public function clean($id)
    {
        exec($this->transmission.' -t '.$id.' --remove');
    }
}
