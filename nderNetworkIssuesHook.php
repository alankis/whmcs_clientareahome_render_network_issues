<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Fetch network issues and render them in clientareahome.tpl
 *
 * @param array $vars Existing defined template variables
 *
 * @return array $networkissues Existing network issues
 */
setlocale(LC_TIME, 'hr_HR.utf8');

function hook_render_network_issues($vars)
{
    // initiate $extraTemplateVariables['networkissues'] array
    $extraTemplateVariables['networkissue'] = array();
    
    // get Capsule::table('tblnetworkissues') data
    $networkissues = Capsule::select("select * from tblnetworkissues WHERE status = 'Reported'");

    // cast PHP stdClass object to array
    $networkissues = json_decode(json_encode($networkissues), true);

    // assign query result to $extraTemplateVariables['networkissues'] array so we can manupulate with it easily
    $extraTemplateVariables['networkissue'] = $networkissues[0];

    // store affecting server id in $affectingserverid
    $affectingserverid = $networkissues[0]['server'];

    // fetch affecting server name value by tblnetworkissues.server JOIN ON tblservers.id
    $affectingservername = Capsule::select('select name from tblservers where id = :id', ['id' => $affectingserverid]);

    // cast PHP stdCLass to array
    $affectingservername = json_decode(json_encode($affectingservername), true);

    // update affecting server value
    $extraTemplateVariables['networkissue']['server'] = $affectingservername[0][name];

    // return array of template variables
    return $extraTemplateVariables;
}
 

// run hook on ClientAreaPageHome
add_hook('ClientAreaPageHome', 1, 'hook_render_network_issues');
