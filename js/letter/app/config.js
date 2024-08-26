// http://help.arcgis.com/en/arcgisserver/10.0/apis/rest/symbol.html#sls

define([], function () {
  return {
    jsonPath:           '/js/letter/data/letters.json',
    letterLineRenderer: {
      type:   'simple',
      label:  '',
      symbol: {
        type:  'esriSLS',
        style: 'esriSLSSolid',
        color: [50, 205, 50, 255],
        width: 2
      },
      description: '',
    }
  };
});
