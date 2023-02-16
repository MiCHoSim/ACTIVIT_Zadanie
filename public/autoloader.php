<?php

/**
 ** Autoloader Automatic loading of classes
 * @param string $class The full name of the class, including the namespace
 * @throws Exception If the class is not found
 */
function autoloader($class)
{
    if (mb_strpos($class, '\\') === FALSE && preg_match('/Helper$/', $class)) // nacita pomocne triedy -> není v namespace a končí na Pomocnik
            $class = 'app\\helper\\' . $class;
    elseif (mb_strpos($class, 'App\\') !== FALSE) // načíta triedy z app
            $class = 'a' . ltrim ($class, 'A'); // zmení App na app
    else // načíta ostatné triedy z vendor
        $class = 'vendor\\' . $class;

    $cesta = str_replace('\\', '/', $class) . '.php'; // Nahrada spätného lomítka a pridanie koncovky k triede
    
    //echo $cesta . "<br/>";

    if (file_exists('../' . $cesta)) // nacitanie popripade vyvolanie vynimky
        include('../' . $cesta);
}

spl_autoload_register("autoloader");