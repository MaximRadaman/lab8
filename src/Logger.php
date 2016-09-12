<?php
/**
 * Custom service Class'.
 *
 */
namespace  Drupal\radaman_maksim_lesson8;

class Logger {
    public function __construct($factory) {
        $this->loggerFactory = $factory;
    }
    public function logToOtherChannels($message) {
        $this->loggerFactory->get('lesson8')->emergency('@placeholder', array('@placeholder'=>$message));
        $this->loggerFactory->get('system')->warning('@placeholder', array('@placeholder'=>$message));
    }
}
