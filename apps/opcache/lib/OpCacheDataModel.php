<?php

    $configuration = opcache_get_configuration();
    $status = opcache_get_status();

    $PageTitle = 'PHP ' . phpversion() . " with OpCache {$configuration['version']['version']}";


    $StatusDataRows = array();

    foreach ($status as $key => $value) {

        if ($key === 'scripts') {
            continue;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if ($v === false) {
                    $value = 'false';
                }
                if ($v === true) {
                    $value = 'true';
                }
                if ($k === 'used_memory' || $k === 'free_memory' || $k === 'wasted_memory') {
                    $v = $size_for_humans(
                        $v
                    );
                }
                if ($k === 'current_wasted_percentage' || $k === 'opcache_hit_rate') {
                    $v = number_format(
                            $v,
                            2
                        ) . '%';
                }
                if ($k === 'blacklist_miss_ratio') {
                    $v = number_format($v, 2) . '%';
                }
                if ($k === 'start_time' || $k === 'last_restart_time') {
                    $v = ($v ? date(DATE_RFC822, $v) : 'never');
                }
                if (THOUSAND_SEPARATOR === true && is_int($v)) {
                    $v = number_format($v);
                }

                $rows[] = "<tr><th>$k</th><td>$v</td></tr>\n";
            }
            continue;
        }
        if ($value === false) {
            $value = 'false';
        }
        if ($value === true) {
            $value = 'true';
        }
        $rows[] = $record;
    }

        return implode("\n", $rows);
    }

    public function getConfigDataRows()
    {
        $rows = array();
        foreach ($configuration['directives'] as $key => $value) {
            if ($value === false) {
                $value = 'false';
            }
            if ($value === true) {
                $value = 'true';
            }
            if ($key == 'opcache.memory_consumption') {
                $value = $size_for_humans($value);
            }
            $rows[] = "<tr><th>$key</th><td>$value</td></tr>\n";
        }

        return implode("\n", $rows);
    }

    public function getScriptStatusRows()
    {
        foreach ($status['scripts'] as $key => $data) {
            $dirs[dirname($key)][basename($key)] = $data;
            $arrayPset($d3Scripts, $key, array(
                'name' => basename($key),
                'size' => $data['memory_consumption'],
            ));
        }

        asort($dirs);

        $basename = '';
        while (true) {
            if (count($d3Scripts) !=1) break;
            $basename .= DIRECTORY_SEPARATOR . key($d3Scripts);
            $d3Scripts = reset($d3Scripts);
        }

        $d3Scripts = $processPartition($d3Scripts, $basename);
        $id = 1;

        $rows = array();
        foreach ($dirs as $dir => $files) {
            $count = count($files);
            $file_plural = $count > 1 ? 's' : null;
            $m = 0;
            foreach ($files as $file => $data) {
                $m += $data["memory_consumption"];
            }
            $m = $size_for_humans($m);

            if ($count > 1) {
                $rows[] = '<tr>';
                $rows[] = "<th class=\"clickable\" id=\"head-{$id}\" colspan=\"3\" onclick=\"toggleVisible('#head-{$id}', '#row-{$id}')\">{$dir} ({$count} file{$file_plural}, {$m})</th>";
                $rows[] = '</tr>';
            }

            foreach ($files as $file => $data) {
                $rows[] = "<tr id=\"row-{$id}\">";
                $rows[] = "<td>" . $format_value($data["hits"]) . "</td>";
                $rows[] = "<td>" . $size_for_humans($data["memory_consumption"]) . "</td>";
                $rows[] = $count > 1 ? "<td>{$file}</td>" : "<td>{$dir}/{$file}</td>";
                $rows[] = '</tr>';
            }

            ++$id;
        }

        return implode("\n", $rows);
    }

    public function getScriptStatusCount()
    {
        return count($status["scripts"]);
    }

    public function getGraphDataSetJson()
    {
        $dataset = array();
        $dataset['memory'] = array(
            $status['memory_usage']['used_memory'],
            $status['memory_usage']['free_memory'],
            $status['memory_usage']['wasted_memory'],
        );

        $dataset['keys'] = array(
            $status['opcache_statistics']['num_cached_keys'],
            $status['opcache_statistics']['max_cached_keys'] - $status['opcache_statistics']['num_cached_keys'],
            0
        );

        $dataset['hits'] = array(
            $status['opcache_statistics']['misses'],
            $status['opcache_statistics']['hits'],
            0,
        );

        $dataset['restarts'] = array(
            $status['opcache_statistics']['oom_restarts'],
            $status['opcache_statistics']['manual_restarts'],
            $status['opcache_statistics']['hash_restarts'],
        );

        if (THOUSAND_SEPARATOR === true) {
            $dataset['TSEP'] = 1;
        } else {
            $dataset['TSEP'] = 0;
        }

        return json_encode($dataset);
    }

    public function getHumanUsedMemory()
    {
        return $size_for_humans($this->getUsedMemory());
    }

    public function getHumanFreeMemory()
    {
        return $size_for_humans($this->getFreeMemory());
    }

    public function getHumanWastedMemory()
    {
        return $size_for_humans($this->getWastedMemory());
    }

    public function getUsedMemory()
    {
        return $status['memory_usage']['used_memory'];
    }

    public function getFreeMemory()
    {
        return $status['memory_usage']['free_memory'];
    }

    public function getWastedMemory()
    {
        return $status['memory_usage']['wasted_memory'];
    }

    public function getWastedMemoryPercentage()
    {
        return number_format($status['memory_usage']['current_wasted_percentage'], 2);
    }

    public function getD3Scripts()
    {
        return $d3Scripts;
    }

    private function _processPartition($value, $name = null)
    {
        if (array_key_exists('size', $value)) {
            return $value;
        }

        $array = array('name' => $name,'children' => array());

        foreach ($value as $k => $v) {
            $array['children'][] = $processPartition($v, $k);
        }

        return $array;
    }

    private function _format_value($value)
    {
        if (THOUSAND_SEPARATOR === true) {
            return number_format($value);
        } else {
            return $value;
        }
    }

    private function _size_for_humans($bytes)
    {
        if ($bytes > 1048576) {
            return sprintf('%.2f&nbsp;MB', $bytes / 1048576);
        } else {
            if ($bytes > 1024) {
                return sprintf('%.2f&nbsp;kB', $bytes / 1024);
            } else {
                return sprintf('%d&nbsp;bytes', $bytes);
            }
        }
    }

    // Borrowed from Laravel
    private function _arrayPset(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;
        $keys = explode(DIRECTORY_SEPARATOR, ltrim($key, DIRECTORY_SEPARATOR));
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if ( ! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

}

?>