<?php

function readCustomers($filename)
{
	$customers = array();

	if (!is_readable($filename)) {
		return $customers;
	}

	$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($lines === false) {
		return $customers;
	}

	foreach ($lines as $line) {
		$parts = explode(';', $line);
		if (count($parts) < 12) {
			continue;
		}

		$customers[] = array(
			'id' => trim($parts[0]),
			'first_name' => trim($parts[1]),
			'last_name' => trim($parts[2]),
			'email' => trim($parts[3]),
			'university' => trim($parts[4]),
			'address' => trim($parts[5]),
			'city' => trim($parts[6]),
			'state' => trim($parts[7]),
			'country' => trim($parts[8]),
			'postal' => trim($parts[9]),
			'phone' => trim($parts[10]),
			'sales' => trim($parts[11]),
		);
	}

	return $customers;
}

function readOrders($customer, $filename)
{
	$orders = array();

	if (!is_readable($filename)) {
		return $orders;
	}

	$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($lines === false) {
		return $orders;
	}

	foreach ($lines as $line) {
		$parts = str_getcsv($line);
		if (count($parts) < 5) {
			continue;
		}

		if (trim($parts[1]) !== (string) $customer) {
			continue;
		}

		$orders[] = array(
			'order_id' => trim($parts[0]),
			'customer_id' => trim($parts[1]),
			'isbn' => trim($parts[2]),
			'title' => trim($parts[3]),
			'category' => trim($parts[4]),
		);
	}

	return $orders;
}
?>