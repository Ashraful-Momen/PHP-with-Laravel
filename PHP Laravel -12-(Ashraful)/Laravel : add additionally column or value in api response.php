 // Use map to add waiting_period to each item in $coverage_amount
        $coverage_amount = $coverage_amount->map(function ($item) use ($waitingPeriods) {
            // Create a copy of the model as an array
            $itemArray = $item->toArray();
            // Add the waiting_period attribute to the array
            $itemArray['waiting_period'] = $waitingPeriods[$item->product_id] ?? 0;
            return $itemArray;
        });
