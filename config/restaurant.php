<?php

return [
    // Tarif pajak (PPN) dalam bentuk desimal. 0.10 = 10%, 0.20 = 20%.
    'tax_rate' => (float) env('TAX_RATE', 0.10),
];