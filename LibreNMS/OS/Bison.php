<?php

namespace LibreNMS\OS;

use LibreNMS\Interfaces\Data\DataStorageInterface;
use LibreNMS\Interfaces\Polling\OSPolling;
use LibreNMS\RRD\RrdDefinition;
use SnmpQuery;

class Bison extends \LibreNMS\OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {

        //PPPOE
        $pppoe_sessions = count(SnmpQuery::walk('BISON-ROUTER-MIB::pppoeIndex')->values());

        if ($pppoe_sessions > 0) {
            $rrd_def = RrdDefinition::make()->addDataset('sessions', 'GAUGE', 0);

            $fields = [
                'sessions' => $pppoe_sessions,
            ];

            $tags = compact('rrd_def');
            $datastore->put($this->getDeviceArray(), 'bison_pppoe_sessions', $tags, $fields);
            $this->enableGraph('bison_pppoe_sessions');
        }

        //IPOE

        $ipoe_sessions = count(SnmpQuery::walk('BISON-ROUTER-MIB::ipoeIndex')->values());


        if ($ipoe_sessions > 0) {
            $rrd_def = RrdDefinition::make()->addDataset('sessions', 'GAUGE', 0);

            $fields = [
                'sessions' => $ipoe_sessions,
            ];

            $tags = compact('rrd_def');
            $datastore->put($this->getDeviceArray(), 'bison_ipoe_sessions', $tags, $fields);
            $this->enableGraph('bison_ipoe_sessions');
        }

        //Deterministic NAT statistic

        $det_nat_stats = SnmpQuery::walk('BISON-ROUTER-MIB::detSnatStat')->values();
        $det_nat_total_maps = $det_nat_stats["BISON-ROUTER-MIB::detSSTotalMaps.0"];
        $det_nat_total_sessions = $det_nat_stats['BISON-ROUTER-MIB::detSSTotalSessions.0'];
        $det_nat_port_map_failures_type1 = $det_nat_stats['BISON-ROUTER-MIB::detSSPortmapFailures.0'];
        $det_nat_port_map_failures_type2 = $det_nat_stats['BISON-ROUTER-MIB::detSSPortmapFailures2.0'];
        $det_nat_session_overflow = $det_nat_stats['BISON-ROUTER-MIB::detSSSessionOverflow.0'];
        $det_nat_no_free_maps = $det_nat_stats['BISON-ROUTER-MIB::detSSNoFreePortmapPorts.0'];

        if ($det_nat_total_maps >= 0) {
            $rrd_def = RrdDefinition::make()->addDataset('maps', 'GAUGE', 0);

            $fields = [
                'maps' => $det_nat_total_maps,
            ];

            $tags = compact('rrd_def');
            $datastore->put($this->getDeviceArray(), 'bison_dnat_maps', $tags, $fields);
            $this->enableGraph('bison_dnat_maps');
        }

        if ($det_nat_total_sessions >= 0 && $det_nat_port_map_failures_type1 >= 0 && $det_nat_port_map_failures_type2 >= 0 && $det_nat_session_overflow >= 0 && $det_nat_no_free_maps >= 0) {

            $rrd_def = RrdDefinition::make()
                ->addDataset('sessions', 'COUNTER', 0)
                ->addDataset('fail_type_1', 'COUNTER', 0)
                ->addDataset('fail_type_2', 'COUNTER', 0)
                ->addDataset('session_overflow', 'COUNTER', 0)
                ->addDataset('no_free_maps', 'COUNTER', 0);
            $fields = [
              'sessions' => $det_nat_total_sessions,
              'fail_type_1' => $det_nat_port_map_failures_type1,
              'fail_type_2' => $det_nat_port_map_failures_type2,
              'session_overflow' => $det_nat_session_overflow,
              'no_free_maps' => $det_nat_no_free_maps,
            ];
            $tags = compact('rrd_def');
            $datastore->put($this->getDeviceArray(), 'bison_dnat_stats', $tags, $fields);
            $this->enableGraph('bison_dnat_stats');
        }





        //Port RX queue utilization

        //NAT translation state counters

        $nat_stats = SnmpQuery::walk('BISON-ROUTER-MIB::portRxQueueUtilizationEntry')->table(1);

        //SNAT44 maps usage stats
    }
}
