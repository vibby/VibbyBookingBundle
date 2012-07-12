// mParallax.js
// MooTools adaptation from 
// jquery.jparallax.js (version: 0.9.1, author: Stephen Band, Project and documentation site: http://webdev.stephband.info/parallax.html)
// Adapatation author : CÃ©dric Pellevillain
// Project and documentation site: http://www.piksite.com/mParallax/

// PRIVATE FUNCTIONS

var clear = "src/clear.gif" // path to the empty image to fix png on IE6

function fixPNG(elt) {
  if(Browser.Engine.trident4) {
    elt.each(function(elmt) {
      elmt.getChildren('img').each(function(el) {
        if((el.src).test('.png', 'i')) {
          el.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + el.src + "',sizingMethod='crop')";
          el.src = clear;
        }
      });
    });
  }
}

function stripFiletype(ref) {
  var x = ref.replace('.html');
  return x.replace('#', '');
}

function initOrigin(l) {
  if (l.xorigin=='left')	{l.xorigin=0;}	else if (l.xorigin=='middle' || l.xorigin=='centre' || l.xorigin=='center')	{l.xorigin=0.5;}	else if (l.xorigin=='right')	{l.xorigin=1;}
  if (l.yorigin=='top')		{l.yorigin=0;}	else if (l.yorigin=='middle' || l.yorigin=='centre' || l.yorigin=='center')	{l.yorigin=0.5;}	else if (l.yorigin=='bottom')	{l.yorigin=1;}
}

function positionMouse(mouseport, localmouse, virtualmouse) {

  var difference = {x: 0, y: 0, sum: 0};
  
	// Set where the virtual mouse is, if not on target
  if (!mouseport.ontarget) {
    
    // Calculate difference
    difference.x    = virtualmouse.x - localmouse.x;
    difference.y    = virtualmouse.y - localmouse.y;
    difference.sum  = Math.sqrt(difference.x*difference.x + difference.y*difference.y);
    
    // Reset virtualmouse
    virtualmouse.x = localmouse.x + difference.x * mouseport.takeoverFactor;
    virtualmouse.y = localmouse.y + difference.y * mouseport.takeoverFactor;
    
    // If mouse is inside the takeoverThresh set ontarget to true
    if (difference.sum < mouseport.takeoverThresh && difference.sum > mouseport.takeoverThresh*-1) {
    	mouseport.ontarget=true;
    }
  }
  // Set where the layer is if on target
  else {
    virtualmouse.x = localmouse.x;
    virtualmouse.y = localmouse.y;
  }
}

function setupPorts(viewport, mouseport) {

	var offset = mouseport.element.getCoordinates();
		
  $extend(viewport, {
    width:		viewport.element.getSize().x,
    height:		viewport.element.getSize().y
  });
  
  $extend(mouseport, {
    width:		mouseport.element.getSize().x,
    height:		mouseport.element.getSize().y,
    top:			offset.top,
    left:			offset.left
  });

}

function parseTravel(travel, origin, dimension) {
  
  var offset;
  var cssPos;
  
  if (typeof(travel) === 'string') {
    if (travel.search(/^\d+\s?px$/) != -1) {
      travel = travel.replace('px', '');
      travel = parseInt(travel, 10);
      // Set offset constant used in moveLayers()
      offset = origin * (dimension-travel);
      // Set origin now because it won't get altered in moveLayers()
      cssPos = origin * 100 + '%';   
      return {travel: travel, travelpx: true, offset: offset, cssPos: cssPos};
    }
    else if (travel.search(/^\d+\s?%$/) != -1) {
      travel.replace('%', '');
      travel = parseInt(travel, 10) / 100;
    }
    else {
      travel=1;
    }
  }
  // Set offset constant used in moveLayers()
  offset = origin * (1 - travel);
  return {travel: travel, travelpx: false, offset: offset}
}

function setupLayer(layer, i, mouseport) {

  var xStuff;
  var yStuff;
  var cssObject = {};

  layer[i] = $extend({
  	width:		$(layer[i].element).getSize().x,
  	height:		$(layer[i].element).getSize().y
  }, layer[i]);

  xStuff = parseTravel(layer[i].xtravel, layer[i].xorigin, layer[i].width);
  yStuff = parseTravel(layer[i].ytravel, layer[i].yorigin, layer[i].height);

  $extend(layer[i], {
  	// Used in triggerResponse
  	diffxrat:    mouseport.width / (layer[i].width - mouseport.width),
  	diffyrat:    mouseport.height / (layer[i].height - mouseport.height),
  	// Used in moveLayers
  	xtravel:     xStuff.travel,
  	ytravel:     yStuff.travel,
  	xtravelpx:   xStuff.travelpx,
  	ytravelpx:   yStuff.travelpx,
  	xoffset:     xStuff.offset,
  	yoffset:     yStuff.offset
  });
  
  // Set origin now if it won't be altered in moveLayers()
  if (xStuff.travelpx) {cssObject.left = xStuff.cssPos;}
  if (yStuff.travelpx) {cssObject.top = yStuff.cssPos;}
  if (xStuff.travelpx || yStuff.travelpx) {layer[i].element.setStyles(cssObject);}
}

function setupLayerContents(layer, i, viewportOffset) {

  var contentOffset;

  // Give layer a content object
  $extend(layer[i], {content: []});
  // Layer content: get positions, dimensions and calculate element offsets for centering children of layers
  for (var n=0; n<layer[i].element.getChildren().length; n++) {
	  
	  if (!layer[i].content[n])          layer[i].content[n]             = {};
	  if (!layer[i].content[n].element)  layer[i].content[n]['element']  = layer[i].element.getChildren()[n];
	  
	  // Store the anchor name if one has not already been specified.  You can specify anchors in Layer Options rather than html if you want.
    if(!layer[i].content[n].anchor && layer[i].content[n].element.getChildren('a').getProperty('name')) {
    	layer[i].content[n]['anchor'] = layer[i].content[n].element.getChildren('a').getProperty('name');
	  }
	  
	  // Only bother to store child's dimensions if child has an anchor.  What's the point otherwise?
	  if(layer[i].content[n].anchor) {
      contentOffset = layer[i].content[n].element.getCoordinates();
	  	$extend(layer[i].content[n], {
	  		width: 		layer[i].content[n].element.getSize().x,
	  		height:		layer[i].content[n].element.getSize().y,
	  		x:			  contentOffset.left - viewportOffset.left,
	  		y:			  contentOffset.top - viewportOffset.top
	  	});
	  	$extend(layer[i].content[n], { 
	  	  posxrat:  (layer[i].content[n].x + layer[i].content[n].width/2) / layer[i].width,
	  	  posyrat:  (layer[i].content[n].y + layer[i].content[n].height/2) / layer[i].height
      });
	  }
  }
}

function moveLayers(layer, xratio, yratio) {

	var xpos;
	var ypos;
	var cssObject;
	
	for (var i=0; i<layer.length; i++) {
    
    // Calculate the moving factor
  	xpos = layer[i].xtravel * xratio + layer[i].xoffset;
    ypos = layer[i].ytravel * yratio + layer[i].yoffset;
    cssObject = {};

  	// Do the moving by pixels or by ratio depending on travelpx
    if (layer[i].xparallax) {
      if (layer[i].xtravelpx) {
        cssObject.marginLeft = xpos * -1 + 'px';
      } 
      else {
        cssObject.left = xpos * 100 + '%';
        cssObject.marginLeft = xpos * layer[i].width * -1 + 'px';
      }
	  }
	  if (layer[i].yparallax) {
      if (layer[i].ytravelpx) {
        cssObject.marginTop = ypos * -1 + 'px';
      }
      else {
        cssObject.top = ypos * 100 + '%';
        cssObject.marginTop = ypos * layer[i].height * -1 + 'px';
      }
    }    
    layer[i].element.setStyles(cssObject);
	}
}


var mParallax = new Class({
  
  Implements: [Options],
	
  //settings
	options: {
    mouseResponse:		    true,						// Sets mouse response
  	mouseActiveOutside:		false,					// Makes mouse affect layers from outside of the mouseport. 
  	triggers:             new Array(),    // Sets triggers
  	triggerResponse:	    true,					  // Sets trigger response
    triggerExposesEdges:  false,          // Sets whether the trigger pulls layer edges into view in trying to centre layer content.
  	xparallax:				    true,						// Sets directions to move in
  	yparallax:				    true,						//
  	xorigin:					    0.5,				    // Sets default alignment - only comes into play when travel is not 1
  	yorigin:					    0.5,				    //
  	xtravel:              1,              // Factor by which travel is amplified
  	ytravel:              1,              //
  	takeoverFactor:		    0.65,						// Sets rate of decay curve for catching up with target mouse position
  	takeoverThresh:		    0.002,					// Sets the distance within which virtualmouse is considered to be on target, as a multiple of mouseport width.
  	frameDuration:        25							// In milliseconds
	},
	
	//initialization
	initialize: function(element,options) {
	
  	this.setOptions(options);
  	
    var settingsLayer = {
  		xparallax:				this.options.xparallax,
  		yparallax:				this.options.yparallax,
  		xorigin:					this.options.xorigin,
  		yorigin:					this.options.yorigin,
  		xtravel:          this.options.xtravel,
  		ytravel:          this.options.ytravel
  	};

    var settingsMouseport = {
  		takeoverFactor:		this.options.takeoverFactor,
  		takeoverThresh:		this.options.takeoverThresh
  	};		
  	
  	// Populate layer array with default settings
  	var layersettings = [];
  	for(var a=2; a<arguments.length; a++) {  		
  		layersettings.push( $merge(settingsLayer, arguments[a]) );  	
    }
    
    if($type(element) == 'array') { var elt = element; } else { var elt = [element]; }
    
    // Iterate matched elements
  	$each(elt, function(elmt) {

      // VAR
    
  		var localmouse = {
  					x:				0.5,
  					y:				0.5
  		};
		
      var virtualmouse = {
  					x:				0.5,
  					y:				0.5
  		};
		
  		var timer = {
  		  running:		false,
  		  frame:			this.options.frameDuration,
  		  fire:				function(x, y) {
  		  	  				  positionMouse(mouseport, localmouse, virtualmouse);
                      moveLayers(layer, virtualmouse.x, virtualmouse.y);
  		  	  				  this.running = setTimeout(function() {
  		  	  				  	if ( localmouse.x!=x || localmouse.y!=y || !mouseport.ontarget ) {
  		  	  				  		timer.fire(localmouse.x, localmouse.y);
  		  	  				  	}
  		  	  				  	else if (timer.running) {
  		  	  				  		timer.running=false;
  		  	  				  	}
  		  	  				  }, timer.frame);
  		  	  				}
  		};

  		var viewport	=	{element: $(elmt)};		

  		var mouseport = {element: $(viewport.element)};
      $extend(mouseport,settingsMouseport);
      $extend(mouseport,{
  		  xinside:          false,		// is the mouse inside the mouseport's dimensions?
  			yinside:	        false,
  			active:		        false,		// are the mouse coordinates still being read?
  			ontarget:         false			// is the top layer inside the takeoverThresh?
  		});
    
  		var layer			= [];
    
      // FUNCTIONS
      
      function matrixSearch(layer, ref, callback) {
        for (var i=0; i<layer.length; i++) {
          var gotcha=false;
          for (var n=0; n<layer[i].content.length; n++) {
            if (layer[i].content[n].anchor==ref) {
              callback(i, n);
              return [i, n];
            }
          }
        }
        return false;
      }
    
      // RUN
      
      setupPorts(viewport, mouseport);

  		// Cycle through and create layers  
      for (var i=0; i<viewport.element.getChildren().length; i++) {
  			// Create layer from settings if it doesn't exist			
  			layer[i]= {element:	$(viewport.element.getChildren()[i])};
  			$extend(layer[i],settingsLayer);
  			$extend(layer[i],layersettings[i]);
  		
  		  setupLayer(layer, i, mouseport);
        
        if (this.options.triggerResponse) {
  		    setupLayerContents(layer, i, viewport.element.getCoordinates());
  		  }
  		}
    
      // Set up layers CSS and initial position
      viewport.element.getChildren().setStyle('position', 'absolute');
  		moveLayers(layer, 0.5, 0.5);
		
  		// Mouse Response
  		if (this.options.mouseResponse) {
  		  document.addEvent('mousemove',function(mouse){
  		
  				// Is mouse inside?
  				mouseport.xinside = (mouse.page.x >= mouseport.left && mouse.page.x < mouseport.width+mouseport.left) ? true : false;
  				mouseport.yinside = (mouse.page.y >= mouseport.top  && mouse.page.y < mouseport.height+mouseport.top)  ? true : false;
          
          // Then switch active on.
  				if (mouseport.xinside && mouseport.yinside && !mouseport.active) {
  					mouseport.ontarget = false;
  					mouseport.active = true;
  				}
  				// If active is on give localmouse coordinates
  				if (mouseport.active) {
  					if (mouseport.xinside) { localmouse.x = (mouse.page.x - mouseport.left) / mouseport.width; }
  					else { localmouse.x = (mouse.page.x < mouseport.left) ? 0 : 1; }
  					if (mouseport.yinside) { localmouse.y = (mouse.page.y - mouseport.top) / mouseport.height; } 
  					else { localmouse.y = (mouse.page.y < mouseport.top) ? 0 : 1; }
  				}
  				
  				// If mouse is inside, fire timer
  				if (mouseport.xinside && mouseport.yinside)  { if (!timer.running) timer.fire(localmouse.x, localmouse.y); }
  				else if (mouseport.active) { mouseport.active = false; }	
          	
  			});
  		}		

      // Trigger Response
    	if (this.options.triggerResponse && this.options.triggers.length != 0) {
    	
      	this.options.triggers.each(function(link) { 
      	
      	  var triggerExposesEdges = this.options.triggerExposesEdges;
      	
        	link.addEvent('click', function() {

		        ref = link.getProperty('href');
		        
        	  ref = stripFiletype(ref);   
                      
            matrixSearch(layer, ref, function(i, n) {
              localmouse.x = layer[i].content[n].posxrat * (layer[i].diffxrat + 1) - (0.5 * layer[i].diffxrat);
              localmouse.y = layer[i].content[n].posyrat * (layer[i].diffyrat + 1) - (0.5 * layer[i].diffyrat);
          
              if (!triggerExposesEdges) {
                if (localmouse.x < 0) localmouse.x = 0;
                if (localmouse.x > 1) localmouse.x = 1;
                if (localmouse.y < 0) localmouse.y = 0;
                if (localmouse.y > 1) localmouse.y = 1;
              }
                    
              mouseport.ontarget = false;
                  
              if (!timer.running) timer.fire(localmouse.x, localmouse.y);
            });
            
            return false;
            
          });
          
        }.bind(this));
    	}

    	window.addEvent('resize', function(){
    	  setupPorts(viewport, mouseport);
    	  for (var i=0; i<layer.length; i++) {
    	    setupLayer(layer, i, mouseport);
        }
      });				

    }.bind(this));	

	}
	
});
