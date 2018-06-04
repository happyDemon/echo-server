<?php

namespace HappyDemon\EchoServer\Storage;


use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable as SwooleTableFacade;

class SwooleTable implements StorageContract
{
    /**
     * @var Table 
     */
    protected $table;

    public function __construct($config)
    {
        // Set up the table
        $table = new Table($config['swoole_table']['rows']);
        $table->column('value', Table::TYPE_STRING, $config['swoole_table']['size']);
        $table->create();

        // Register the table
        SwooleTableFacade::add($config['channels_table'], $table);
        $this->table = $table;
    }

    /**
     * Add a user to a channel.
     *
     * @param string $channel
     * @param int    $userId
     * @param string $fd
     *
     * @return $this|SwooleTable
     */
    public function addToChannel(string $channel, int $userId, string $fd)
    {
        $users = $this->getValue($channel);

        if(collect($users)->where('user_id', $users)->first() !== null) return $this;

        $users[] = [
            'user_id' => $userId,
            'fd' => $fd
        ];

        return $this->setValue($channel, $users);
    }

    /**
     * Remove a user from a channel.
     *
     * @param string $channel
     * @param int    $userId
     *
     * @return $this|SwooleTable
     */
    public function removeFromChannel(string $channel, int $userId)
    {
        $users = collect($this->getValue($channel));

        if($users->where('user_id', $users)->first() !== null) return $this;

       return $this->setValue(
           $channel,
           $users->where('user_id', '!=', $userId)->values()->toArray()
       );
    }

    /**
     * Get all users in a specific channel.
     *
     * @param string $channel
     *
     * @return array|mixed
     */
    public function getMembers(string $channel)
    {
        return $this->getValue($channel);
    }

    /**
     * Retrieve a value from a column.
     *
     * @param string $key
     *
     * @return array|mixed
     */
    protected function getValue(string $key)
    {
        $value = $this->table->get($key);

        return $value ? json_decode($value['value'], true) : [];
    }

    /**
     * Write a value to the database
     * @param string $key
     * @param array  $value
     *
     * @return $this
     */
    public function setValue(string $key, array $value)
    {
        $this->table->set($key, [
            'value' => json_encode($value)
        ]);

        return $this;
    }
}