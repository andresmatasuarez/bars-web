#target photoshop
var home = new File('~');

var files = home.openDlg('Select images to process', 'Images PNG:*.png;', true);

if (files != null && files.length != 0){
  
  var output = new Folder(files[0].parent + '/output/');
  if(!output.exists){
    output.create();
  }
  
  for (var i = 0; i < files.length; i++){
    var f = open(files[i]);
    
    f.changeMode(ChangeMode.GRAYSCALE);
    f.activeLayer.opacity = 70;

    var filename = files[i].name.slice(0, -4);
    var saveFile = new File(output.absoluteURI + '/' + filename + '_hover.png');
    pngSaveOptions = new PNGSaveOptions();
    pngSaveOptions.compression = 0;
    pngSaveOptions.interlaced = false;
    
    f.saveAs(saveFile, pngSaveOptions, true, Extension.LOWERCASE); 
    
    f.close(SaveOptions.DONOTSAVECHANGES);
    
  }
 
}