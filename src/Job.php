<?php

namespace G;

interface Job
{
    function desc();

    function help();

    function execute($args = []);
}