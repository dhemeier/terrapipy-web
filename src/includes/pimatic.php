<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__))
{
    header('Location: /');
    exit;
}

function getPimaticData($pimaticUsername, $pimaticPassword, $pimaticHost, $pimaticUrl)
{
    $pageUrl = "http://$pimaticUsername:$pimaticPassword@$pimaticHost$pimaticUrl";

    $curl    = curl_init();
    $headers = [];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_URL, $pageUrl);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    $json = curl_exec($curl);
    curl_close($curl);

    return json_decode($json);
}

function getPages($pimaticUsername, $pimaticPassword, $pimaticHost, $pimaticTerrariumKeyword)
{
    $pageData = getPimaticData($pimaticUsername, $pimaticPassword, $pimaticHost, "/api/pages");

    if ($pageData === NULL)
    {
        return [];
    }

    $terrarium = [];
    foreach ($pageData->pages as $page)
    {
        if (stripos($page->id, $pimaticTerrariumKeyword) !== FALSE ||
            stripos($page->name, $pimaticTerrariumKeyword) !== FALSE)
        {
            $terrarium[$page->id] = $page;
        }
    }

    return $terrarium;
}

function getRules($pimaticUsername, $pimaticPassword, $pimaticHost)
{
    $pageData = getPimaticData($pimaticUsername, $pimaticPassword, $pimaticHost, "/api/rules");

    if ($pageData === NULL)
    {
        return [];
    }

    $rules = [];
    foreach ($pageData->rules as $rule)
    {
        $rules[$rule->id] = $rule;
    }

    return $rules;
}

function getVariables($pimaticUsername, $pimaticPassword, $pimaticHost)
{
    $pageData = getPimaticData($pimaticUsername, $pimaticPassword, $pimaticHost, "/api/variables");

    if ($pageData === NULL)
    {
        return [];
    }

    $variables = [];
    foreach ($pageData->variables as $variable)
    {
        $variables[$variable->id] = $variable;
    }

    return $variables;
}

function getDevices($pimaticUsername, $pimaticPassword, $pimaticHost)
{
    $pageData = getPimaticData($pimaticUsername, $pimaticPassword, $pimaticHost, "/api/devices");

    if ($pageData === NULL)
    {
        return [];
    }

    $devices = [];
    foreach ($pageData->devices as $device)
    {
        $devices[$device->id] = $device;
    }

    return $devices;
}

function deviceOnPage($deviceId, $pageId, $terrariumPages)
{
    foreach ($terrariumPages as $tp)
    {
        if ($pageId !== $tp->id)
        {
            continue;
        }

        foreach ($tp->devices as $d)
        {
            //echo $d->deviceId."<br>";
            //echo $deviceId."<br>";
            if ($d->deviceId === $deviceId)
            {
                return TRUE;
            }
        }
    }

    return FALSE;
}

