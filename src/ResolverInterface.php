<?php

namespace Hiraeth\Utils\Signal;

interface ResolverInterface
{
	public function __invoke($signal);
}
