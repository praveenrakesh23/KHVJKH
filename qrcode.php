<?php
/*
 * PHP QR Code encoder
 * (simplified version - just the bare minimum to generate QR codes)
 */

class QRCode {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function generate() {
        // Use Google Charts API to generate QR code
        $url = 'https://chart.googleapis.com/chart?';
        $params = array(
            'cht' => 'qr',
            'chs' => '300x300',
            'chl' => $this->data,
            'chld' => 'L|0'
        );
        
        $url .= http_build_query($params);
        return $url;
    }
}
?> 