/**  ———————————————————————————————————————————— 1 CYCLESTREET —————————————————————— **/

.cyclestreet {
  
  ::case {
    line-color:#33ffff;
    line-join: round;
  }
  ::fill {
    line-color:#33ffff;
    line-join: round;                         
  }
  [zoom>=10]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=11]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=12]
    ::fill {
    line-width: 1;
    line-opacity:0.5;
    }
    ::case {
    line-width: 3;
    line-opacity:0.2;
  }
  
  [zoom>=13]
  ::case {
    line-width: 5;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=14]
  ::case {
    line-width: 7;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=15]
  ::case {
    line-width: 9;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  [zoom>=16]
  ::case {
    line-width: 11;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  }

/**  ———————————————————————————————————————————— cyclestreet —————————————————————— **/  

.cycleway {
  
  ::case {
    line-color:#33ff33;
    line-join: round;
  }
  ::fill {
    line-color:#33ff33;
    line-join: round;                         
  }

  [zoom>=10]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=11]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=12]
    ::fill {
    line-width: 1;
    line-opacity:0.5;
    }
    ::case {
    line-width: 3;
    line-opacity:0.2;
  }
  
  [zoom>=13]
  ::case {
    line-width: 5;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=14]
  ::case {
    line-width: 7;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=15]
  ::case {
    line-width: 9;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  [zoom>=16]
  ::case {
    line-width: 11;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  }

/**  ———————————————————————————————————————————— 2 SAFE PARKING —————————————————————— **/  


  .safe_parking {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#ff4455;

  [zoom>=10] {
    marker-width:1;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:3;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:4;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 0.8;
    marker-width:9;
    marker-allow-overlap:true;
    point-file: url(images/safeparking.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.09)";
  }

  
  [zoom>=15] {
    point-opacity: 0.9;
    marker-width:12;
    marker-allow-overlap:true;
    point-file: url(images/safeparking.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.12)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:24;
    marker-allow-overlap:true;
    point-file: url(images/safeparking.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.2)";
  }
   
}

/**  ———————————————————————————————————————————— 3 WIFI —————————————————————— **/  


.free_wifi {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#33ffff;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 1;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }

  
  [zoom>=15] {
    point-opacity: 1;
    marker-width:25;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.25)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:30;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.3)";
  }
   
}
/**  ———————————————————————————————————————————— 3b WIFI VENUES —————————————————————— **/


.wifi_venues {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#33ffff;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }

  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }

  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  }

  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }

  [zoom>=14] {
    point-opacity: 1;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }


  [zoom>=15] {
    point-opacity: 1;
    marker-width:25;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.25)";
  }


  [zoom>=16] {
    point-opacity: 1;
    marker-width:30;
    marker-allow-overlap:true;
    point-file: url(images/wifi.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.3)";
  }

}

/**  ———————————————————————————————————————————— 4 REWARD —————————————————————— **/  


.rewards {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#ffff66;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 1;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/reward3.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }

  
  [zoom>=15] {
    point-opacity: 1;
    marker-width:25;
    marker-allow-overlap:true;
    point-file: url(images/reward3.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.25)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:30;
    marker-allow-overlap:true;
    point-file: url(images/reward3.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.3)";
  }
   
}


/**  ———————————————————————————————————————————— 5 RACK —————————————————————— **/  


.racks {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#cccccc;


   [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 

  [zoom>=13] {
    point-opacity: 0.8;
    marker-width:9;
    marker-allow-overlap:true;
    point-file: url(images/rack2.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.09)";
  }

  [zoom>=14] {
    point-opacity: 0.8;
    marker-width:9;
    marker-allow-overlap:true;
    point-file: url(images/rack2.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.09)";
  }

  [zoom>=15] {
    point-opacity: 0.9;
    marker-width:12;
    marker-allow-overlap:true;
    point-file: url(images/rack2.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.12)";
  }
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:24;
    marker-allow-overlap:true;
    point-file: url(images/rack2.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.24)";
  }
   
}

/**  ———————————————————————————————————————————— 6 TRAIN —————————————————————— **/  
/**  ———————————————————————————————————————————— A track —————————————————————— **/  

.train_track {
  ::line {
    line-width:0.8;
    line-color:#cecece;
    line-opacity:0.8;
    line-join: round;
  }
  ::hatch {
    line-width:1;
    line-color:#cecece;
    line-opacity:0.8;
  }
    
[zoom>=10] {
    ::line { line-width:0.5;}
    ::hatch { line-width: 0;}
    }
  
 [zoom>=11] {
    ::line { line-width:0.8;}
    ::hatch { line-width:0;}
    } 

[zoom>=12] {
    ::line { line-width:1;}
    ::hatch { line-width:0;}
    }
  
  [zoom>=13] {
  ::hatch { line-width: 3; line-dasharray: 1, 9; }
    }  
  
 [zoom>=14] {
  ::hatch { line-width: 3; line-dasharray: 1, 9; }
    }  
  
[zoom>=15] {
  ::hatch { line-width: 5; line-dasharray: 1, 5; }
    }  
  
[zoom>=16] {
  ::hatch { line-width: 5; line-dasharray: 1, 5; }
    }  
  }
  
/**  ——————————————————————————————————————————— 6 TRAIN —————————————————————— **/  
/**  ——————————————————————————————————————————— B station ————————————————————— **/  


.train_station {
  marker-line-width:0;
  marker-opacity:0.6;
  marker-fill:#cecece;

  [zoom>=10] {
    marker-width:3;
    marker-allow-overlap:true;
  }

  [zoom>=11] {
    marker-width:5;
    marker-allow-overlap:true;
  }

  [zoom>=12] {
    point-opacity: 0.8;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/train_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }

  [zoom>=13] {
    point-opacity: 0.9;
    marker-width:20;
    marker-allow-overlap:true;
    point-file: url(images/train_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.20)";
  }

  [zoom>=14] {
    point-opacity: 1;
    marker-width:30;
    marker-allow-overlap:true;
    point-file: url(images/train_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.3)";
  }

  [zoom>=15] {
    point-opacity: 1;
    marker-width:40;
    marker-allow-overlap:true;
    point-file: url(images/train_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.4)";
  }
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:50;
    marker-allow-overlap:true;
    point-file: url(images/train_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.5)";
  }
  
}


/**  ———————————————————————————————————————————— 7 BIKESHOP —————————————————————— **/  


.bike_shop {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#2ba0ff;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 0.8;
    marker-width:9;
    marker-allow-overlap:true;
    point-file: url(images/bikeshop.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.12)";
  }

  
  [zoom>=15] {
    point-opacity: 0.9;
    marker-width:12;
    marker-allow-overlap:true;
    point-file: url(images/bikeshop.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:24;
    marker-allow-overlap:true;
    point-file: url(images/bikeshop.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.25)";
  }
   
}


/**  ———————————————————————————————————————————— 8 AIRPUMP —————————————————————— **/  


.air_pump {
  marker-line-width:0;
  marker-opacity:0.8;
  marker-fill:#ae83f7;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 0.8;
    marker-width:9;
    marker-allow-overlap:true;
    point-file: url(images/airpump2.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.11)";
  }

  
  [zoom>=15] {
    point-opacity: 0.9;
    marker-width:12;
    marker-allow-overlap:true;
    point-file: url(images/airpump2.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.14)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:24;
    marker-allow-overlap:true;
    point-file: url(images/airpump2.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.26)";
  }
   
}


/**  ———————————————————————————————————————————— 9 PUBLIC BIKE —————————————————————— **/  


.public_bike {
  marker-line-width:0;
  marker-opacity:0.7;
  marker-fill:#ffffff;

  [zoom>=10] {
    marker-width:2;
    marker-allow-overlap:true;
  }
  
  [zoom>=11] {
    marker-width:3;
    marker-allow-overlap:true;
  }
  
  [zoom>=12] {
    marker-width:4;
    marker-allow-overlap:true;
  } 
     
  [zoom>=13] {
    marker-width:5;
    marker-allow-overlap:true;
  }
  
  [zoom>=14] {
    point-opacity: 1;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/publicbike.svg);
  point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }

  
  [zoom>=15] {
    point-opacity: 1;
    marker-width:25;
    marker-allow-overlap:true;
    point-file: url(images/publicbike.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.25)";
  }
  
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:30;
    marker-allow-overlap:true;
    point-file: url(images/publicbike.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.3)";
  }
   
}


/**  ———————————————————————————————————————————— 10 SUBWAY —————————————————————— **/  
/**  ———————————————————————————————————————————— A track —————————————————————— **/  

.subway_track {
  ::line {
    line-width:0.8;
    line-color:#5d5d5e;
    line-opacity:0.7;
    line-join: round;
  }
  ::hatch {
    line-width:1;
    line-color:#5d5d5e;
    line-opacity:0.8;
  }
    
[zoom>=10] {

  ::line {
    line-width: 0.8;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}
  
[zoom>=11] {

  ::line {
    line-width: 1;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}

[zoom>=12] {

  ::line {
    line-width: 2;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}
  
  
[zoom>=13] {

  ::line {
    line-width: 3;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}

 [zoom>=14] {

  ::line {
    line-width: 3;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}
  
  
[zoom>=15] {

  ::line {
    line-width: 5;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}
  
[zoom>=16] {

  ::line {
    line-width: 5;
    line-color: #5d5d5e;
    line-opacity: 0.8;
    }
}
}



/**  ——————————————————————————————————————————— 10 SUBWAY —————————————————————— **/  
/**  ——————————————————————————————————————————— B station —————————————————————— **/  


.subway_station {
  marker-line-width:0;
  marker-opacity:0.6;
  marker-fill:#5d5d5e;

  [zoom>=10] {
    marker-width:3;
    marker-allow-overlap:true;
  }

  [zoom>=11] {
    marker-width:5;
    marker-allow-overlap:true;
  }

  [zoom>=12] {
    point-opacity: 0.8;
    marker-width:10;
    marker-allow-overlap:true;
    point-file: url(images/subway_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.10)";
  }

  [zoom>=13] {
    point-opacity: 0.9;
    marker-width:15;
    marker-allow-overlap:true;
    point-file: url(images/subway_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.15)";
  }

  [zoom>=14] {
    point-opacity: 1;
    marker-width:20;
    marker-allow-overlap:true;
    point-file: url(images/subway_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.20)";
  }

  [zoom>=15] {
    point-opacity: 1;
    marker-width:35;
    marker-allow-overlap:true;
    point-file: url(images/subway_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.35)";
  }
  
  [zoom>=16] {
    point-opacity: 1;
    marker-width:45;
    marker-allow-overlap:true;
    point-file: url(images/subway_station.svg);
    point-allow-overlap:true;
    point-transform:"scale(0.45)";
  }
    
}

/**  ———————————————————————————————————————————— 11 FERRY —————————————————————— **/  
/**  ———————————————————————————————————————————— a track —————————————————————— **/  

.ferry {
  ::line {
    line-width:0.8;
    line-color:#cca37a;
    line-opacity:0.4;
    line-join: round;
  }
    
[zoom>=10] {

  ::line {
    line-width: 0.8;
    }
}
  
[zoom>=11] {

  ::line {
    line-width: 1;
    }
}

[zoom>=12] {

  ::line {
    line-width: 2;
    }
}
  
  
[zoom>=13] {

  ::line {
    line-width: 3;
    }
}

 [zoom>=14] {

  ::line {
    line-width: 3;
    }
}
  
  
[zoom>=15] {

  ::line {
    line-width: 5;
    }
}
  
[zoom>=16] {

  ::line {
    line-width: 5;
    }
}
}

/**  ———————————————————————————————————————————— 11 STREET —————————————————————— **/  
/**  ———————————————————————————————————————————— Cobblestone —————————————————————— **/  
.cobblestone {
  
  ::case {
    line-color:#ff4455;
    line-join: round;
  }
  ::fill {
    line-color:#ff4455;
    line-join: round;                         
  }

  [zoom>=10]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=11]
    ::fill {
    line-width: 0.5;
    line-opacity:0.5;
    }
    ::case {
    line-width: 2;
    line-opacity:0.2;
  }
  
  [zoom>=12]
    ::fill {
    line-width: 1;
    line-opacity:0.5;
    }
    ::case {
    line-width: 3;
    line-opacity:0.2;
  }
  
  [zoom>=13]
  ::case {
    line-width: 5;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=14]
  ::case {
    line-width: 7;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 1;
    line-opacity:0.5;
  }

  [zoom>=15]
  ::case {
    line-width: 9;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  [zoom>=16]
  ::case {
    line-width: 11;
    line-opacity:0.2;
  }
  ::fill {
    line-width: 2;
    line-opacity:0.5;
  }
  
  }