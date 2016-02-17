# php_hei_elasticsearch_client

        
        require_once dirname(__FILE__).'/hei_elasticsearch_client.php';
        
        $params = array(
        	'hosts' => array('localhost:9200'),
        );
        $client = new hei_elasticsearch_client($params);
        
        $params = array(
        	'body'  => array('testField' => 'abc'),
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'id'    => 'my_id',
        );
        $ret = $client->exec($params);
        var_dump($ret);
        
        
        $getParams = array(
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'id'    => 'my_id',
        	'method' => 'get',
        );
        $ret = $client->exec($getParams);
        var_dump($ret);
        
        
        $updateParams = array(
            'index' => 'my_index',
            'type'  => 'my_type',
            'id'    => 'my_id',
            'action' => '_update',
            'body'  => array(
                'doc' => array(
                    'uptestField' => 'upabc',
                ),
            ),
        );
        $ret = $client->exec($updateParams);
        var_dump($ret);
        
        
        $params = array(
            'action' => '_count',
            'index' => 'zk',
            'type'  => 'article',
        );
        $ret = $client->exec($params);
        var_dump($ret);
        
        $searchParams = array(
        	'index' => 'my_index',
        	'type'  => 'my_type',
        	'action' => '_search',
        	'body'  => array(
        		'query' => array(
        			'match' => array(
        				'testField' => 'abc',
        			),
        		),
        	),
        );
        $ret = $client->exec($searchParams);
        var_dump($ret);
        
        
        $deleteParams = array(
        	'index' => 'my_index',
        	'type' => 'my_type',
        	'id' => 'my_id',
        	'method' => 'delete',
        );
        $ret = $client->exec($deleteParams);
        var_dump($ret);
        
        
        $deleteParams = array(
        	'index' => 'my_index',
        	'method' => 'delete',
        );
        $ret = $client->exec($deleteParams);
        var_dump($ret);
        
        
        $indexParams = array(
        	'index' => 'my_index',
        	'body' => array(
        		'settings' => array(
        			'number_of_shards' => 2,
        			'number_of_replicas' => 0,
        		),
        	),
        	'method' => 'put',
        );
        $ret = $client->exec($indexParams);
        var_dump($ret);



        $params = array(
            'body'  => array(
                '{ "index":  { "_index": "website", "_type": "blog", "_id": "1234" }}',
                '{ "title":    "But we can update it" }',
                '{ "index":  { "_index": "website", "_type": "blog", "_id": "12434" }}',
                '{ "title":    "But we can update it" }',
            ),
            'action' => '_bulk',
        );
        $ret = $client->exec($params);
        
        var_dump($ret);
        
        
        
        $params = array(
            'body'  => array(
                array(
                    'index' => array(
                        '_index' => 'website',
                        '_type' => 'blog',
                        '_id' => '123',
                    ),
                ),
                array(
                    'title' => 'But we can update it',
                ),
                array(
                    'index' => array(
                        '_index' => 'website',
                        '_type' => 'blog',
                        '_id' => '12345',
                    ),
                ),
                array(
                    'title' => 'But we can update it',
                ),
            ),
            'action' => '_bulk',
        );
        $ret = $client->exec($params);
        
        var_dump($ret);
