// JavaScript Document

(function($) {
	
    $.fn.customFadeIn = function(speed, callback) {
        $(this).fadeIn(speed, function() {
            if(jQuery == null || jQuery.browser==null)
                return;
            if(jQuery.browser.msie)
                $(this).get(0).style.removeAttribute('filter');
            if(callback != undefined)
                callback();
        });
    };
        
    $.fn.customFadeOut = function(speed, callback) {
        $(this).fadeOut(speed, function() {
            if(jQuery == null || jQuery.browser==null)
                return;
            if(jQuery.browser.msie)
                $(this).get(0).style.removeAttribute('filter');
            if(callback != undefined)
                callback();
        });
    };

    $.fn.buttonBarAnimate = function (options){
        var defaults = {	
            recursive: false
        };
		
        var options = $.extend(defaults, options);  
		
        if(options.recursive){
            $(this).children('li').each(function(index, element) {
                $(this).buttonBarAnimate();
            });
            return;
        }
		
        var speed = 500;
        var currentObject  = this;
        var parentX = $(this).width();
        var orY = $(this).children('ul:first').height();
        var orX = $(this).children('ul:first').width();
		
        $(this).mouseenter(function(e) {
            show($(this).children('ul:first'));
        });
        $(this).mouseleave(function(e) {
            hide($(this).children('ul:first'));
        });
		
        function hide(child){
            $(child).stop();
            $(child).animate({
                'opacity':'0',
                'width':'0px',
                'height':'0px'
            },speed, function(){
                $(child).css({
                    'display':'none'
                });
                $(child).height(orY);
                $(child).width(orX);
            });
        }
        function show(child){
            $(child).stop();
            $(child).css({
                'display':'block'
            });
            $(child).css({
                'margin-left': '-' + ((orX/2) - (parentX / 2)) + 'px'
            });
            $(child).height(0);
            $(child).width(0);
			
            $(child).animate({
                'opacity':'1',
                'width':orX,
                'height':orY
            },speed);
        }
    }
	
    /*Animacion por cuadros de una imagen*/
    $.fn.frameAnimation = function(options){	
        var defaults = {	
            frameWidth: 100,
            frameHeight: 100,
            frames: 4,
            speed: 500
        };
		
        var options = $.extend(defaults, options);  
		
        var currentObject = this;
        var position = 0;
        var maxSize = options.frameWidth * options.frames;
        $(this).height(options.frameHeight);
        $(this).width(options.frameWidth);
        animates();
		
        function animates(){
            setTimeout(function(){
                $(currentObject).css({
                    'background-position': '-' + position + 'px'
                }); 
                position += options.frameWidth;
                if(position >= maxSize)
                    position = 0;
                animates();			
            }, options.speed);
        }
    }

    /*Anima los elmentos con la configuracion especificada*/
    $.fn.animar = function(options){		
	
        var defaults = {	
            events: '',
            recursive: false,
            loops: false
        };
		
        var options = $.extend(defaults, options);
        /*SOLUCION:
		El problema entre hilos, una sola variable para todos los efectos:
		Los ultimos valores de inicio se marcan antes de comenzar
		la animcacion correspondiente.
		Los valores son almacenados para que puedan ser aplicados en el momento justo
		en que se hace la animación.
         */
        var loopComplete = true;
        var count = 0;
        var countExe = 0;
        var allowAnimate = {};
        var cssValues = {};
        var cssValuesOriginal = {};
        var currentGlobalObject = this;
        var speeds = [];
        var ends = {};	
        /*PROBLEMA: Este atributo se toma como GLOBAL, se ejecuta en todas las animaciones*/
        var timeout = {};
        looper();
		
        function looper(){
            if(loopComplete){
                //alert('loop complete');
                count = 0;
                countExe = 0;
                loadAnimations(currentGlobalObject);
            }
            if(options.loops)
                setTimeout(looper, 500);
        }
		
        function loadAnimations(object){
            loopComplete = false;
            doAnimation(object);
            if(options.recursive){
                $(object).children('*').each(function() {
                    $(this).animar(options);
                });
            }
        }
		
        function getPair(str, separator, index, pred){
            var splited = str.split(separator);
            if(splited.length < index + 1)
                return '';
            var value = splited[index];
            return value;
        }
		
        function getOrignalValue(element, attrib){
            var cssOriginal = {};
            var valueOriginal = $(element).css(attrib);
            cssOriginal[attrib] = valueOriginal;
            return cssOriginal;
        }
		
        function doAnimation(object){
            var attr = $(object).attr('name');
            if(attr == null)
                return;
				
            var currentObject = object;
            attr = attr.toLowerCase();
            attr = attr.replace('\n','');
            var splittedCommands = attr.split('|');
			  
            for(str in splittedCommands){
                var commandSTR = splittedCommands[str];
                var splittedValues = commandSTR.split(';');
				
                var events = 'now';
                var cssStartTmp = {};
                var cssOriginalTmp = [];
				
                ++count;
                var inc = 0;
                for(set in splittedValues){
                    var setSTR = splittedValues[set];
                    var attrib = getPair(setSTR, ':', 0, '');
                    var value = getPair(setSTR, ':', 1, '');
                    /*alert('name: ' + name + '\nvalue: ' + value);*/
				  
                    if(attrib == 'speed'){
                        speeds[count] = parseInt(value);
                        continue;
                    }
                    if(attrib == 'timeout'){
                        timeout[count] = parseInt(value);
                        continue;
                    }
                    if(attrib == 'loops'){
                        options.loops = (value == 'true');
                        continue;
                    }
                    if(attrib == 'ends'){
                        ends[count] = value;
                        continue;
                    }
                    if(attrib == 'events'){
                        events = value;
                        continue;
                    }
				  
                    cssStartTmp[attrib] = value;
                    cssOriginalTmp[inc] = attrib;
                    ++inc;
                }
                cssValues[count] = cssStartTmp;
                cssValuesOriginal[count] = cssOriginalTmp;
				
                allowAnimate[count] = (options.events == events);
                
				
                switch(events){
                    case 'click':
                        $(object).click(function(e) {
                            doTimeOutAnim();
                        });
                        break;
                    case 'mousein':
                        $(object).mouseenter(function(e) {
                            doTimeOutAnim();
                        });
                        break;
                    case 'mouseout':
                        $(object).mouseleave(function(e) {
                            doTimeOutAnim();
                        });
                        break;
                    default:
                        //doTimeOutAnim();
                        break;
                }
                
				
                function doTimeOutAnim(){
                    //alert(count + '> ' + timeout[count] + '\n' + commandSTR);
                    setTimeout(function(){
                        /*PROBLEMA AQUI:
                         * Al iniciar la animacion se tienen que respetar
                         * los tiempos antes de la animacion (timeout)
                         * por lo contrario, si una animacion se ejecuta antes,
                         * podria desordenar el orden de ejecución y por lo tanto
                         * los valores obtenidos por el indice.
                         **/
                        ++countExe;
                        if(allowAnimate[countExe]){
                            //alert('animates ' + countExe);
                            animates();
                        }
                    }, timeout[count]);
                }
                doTimeOutAnim();
				
                function animates(){
                    var values = getBeginValues(countExe);
                    $(currentObject).css(values['start']);
                    $(currentObject).animate(values['stop'], speeds[countExe], function(){
                        if(countExe == count)
                            loopComplete = true;
                    });
                }
                
                function getBeginValues(animateIndex){
                    var cssOriginal =  {};
                    var arrays = cssValuesOriginal[animateIndex];
                    for(tmp in arrays){
                        var selected = arrays[tmp];
                        cssOriginal[selected] = $(currentObject).css(selected);
                    //alert(selected + ' - ' + $(currentObject).css(selected));
                    }
                    var cssStart = cssValues[animateIndex];
                    var value1;
                    var value2;
				  
                    //alert(ends[animateIndex]);
                    if (ends[animateIndex] == 'set'){	
                        value1= cssOriginal;
                        value2 = cssStart;
                    } else{
                        value1= cssStart;
                        value2 = cssOriginal;
                    }
                    var returns = {};
                    returns['start'] = value1;
                    returns['stop'] = value2;
                    return returns;
                }
            }
        }
    }
	
    $.fn.fades = function(){
        this.each(function(){ 
            $(this).css({
                'display':'none'
            });
            $(this).fadeIn('slow', function() {
                });
        });
    };
	
    /*Deslizador de imagenes*/
    $.fn.slider = function(options){
		
        var defaults = {	
            adjust: false,
            width: 500,
            height: 300,
            changeTime: 10000,
            changeActive: true,
            speed: 1500,
            changeOpacity: false,
            enableAnimations: true,
            arrowPreview: 'nav1',
            arrowNext: 'nav2',
            showMinSlider: true
        };
		
        var options = $.extend(defaults, options);
		
        var orX=$(this).width();
        var orY=$(this).height();
        var currentParent = $(this);
        var index = 0;
        var index_min = 0;
        var width = options.width;
        var height = options.height;
        var adjustImage = options.adjust;
        var changeTime = options.changeTime;
        var changeActive = options.changeActive;
        var changeOpacity = options.changeOpacity;
        var enableAnimations = options.enableAnimations;
        var showMinSlider = options.showMinSlider;	
        var speed = options.speed;
        var preChanged = false;
        var itemWidth = $(currentParent)
        .children('#slides_min')
        .children('ul')
        .children('li:first').width();
		
        $(this).children('#slides').height(0);
        $(this).children('#slides').children('ul').width(width * (getCount() + 1));
        $(this).children('#slides_min').children('ul').width(itemWidth * (getCount()+ 1));
        $(this).children('#slides').addClass('slider-img-container');
        $(this).children('#slides_min').addClass('slider-img-small-container');
        if(!showMinSlider){
            $(this).children('#slides_min').css({
                'display':'none'
            });
        }
        hideAll();
        $(this).addClass('navegador');
        $(this).width(width);
        set();
        rotateImage(0, -1);
		
        $(this).children('#slides').children('ul').children('li:first').children('img:first').load(function(e) {
            
            var oldState = changeActive;
            changeActive = false;
            changeImage();
            changeActive = oldState;
        });		
		
        function changeImage(){
            setTimeout(changeImage, changeTime);
            if(changeActive){
                if(!preChanged)
                    rotateImage(1, -1);
                else
                    preChanged = false;
            }
        }
		
        function set(){	
            $(currentParent).children('#slides').children('ul').children('li').each(function(index, element) {
                element = $(this).children('#tag');
                if(changeOpacity){
                    $(this).fadeOut(100);
                }
                $(element).addClass('slider-box-details');
            });
			
            var count = 0;		
            $(currentParent).children('#slides_min').children('ul').children('li').each(function(index, element) {
                element = $(this).children('img:first');
                $(element).attr('align','top');
                $(element).attr('width', '50px');
                $(element).attr('height', '30px');
                $(this).attr('id', count);
                $(element).attr('id', count);
                $(element).click(function(e) {
                    preChanged = true;
                    rotateImage(0, $(this).attr('id'));
                });
                $(this).click(function(e) {
                    preChanged = true;
                    rotateImage(0, $(this).attr('id'));
                });
                ++count;
            });
        }
		
        function getCount(){
            return $(currentParent).children('#slides').children('ul').children('li').length - 1;
        }
		
        function hideAll(){
            $(currentParent).children('#slides').children('ul').children('li').each(function(index, element) {
                $(this).width(width);
                $(this).height(height);
            });
        }
        
        function checkIndex(number){
            if(getCount() < number)
                number = 0;
            if(0 > number)
                number = getCount();
            return number;
        }
		
        function rotateImage(dir, to){
            /*$(parents).children('img:eq(' + index + ')').css({'display':'none'});*/
            /*hideAll(parents);*/
			
            /*var item = $(parents).children('img:eq(' + index + ')');
			$(item).css({'display':'none'});*/
            var item = $(currentParent)
            .children('#slides')
            .children('ul')
            .children('li:eq(' + index + ')');
            //alert(enableAnimations);
            if(enableAnimations){
                if(dir != 0 || to != -1){
                    //alert('ends ' + index);
                    $(item).animar({
                        events:'ends', 
                        recursive:true
                    });
                }
                if(changeOpacity){
                    $(item).fadeOut(speed);
                    $(item).customFadeOut(speed);
                }
            }
			
            if(to != -1){
                index = parseInt(to);
            } else {
                index += dir;
                index = checkIndex(index);
            }
			
            /*item = $(parents).children('img:eq(' + index + ')');
			$(item).css({'display':'block'});*/
			
            item = $(currentParent).children('#slides')
            .children('ul');
			
            $(item).animate({
                'margin-left': ('-' + (index * width) + 'px')
            }, 1500);
			
            item = $(currentParent)
            .children('#slides')
            .children('ul')
            .children('li:eq(' + index + ')');
			
            if(enableAnimations){
                //alert('begin ' + index);
                $(item).animar({
                    events:'begin', 
                    recursive:true
                });     
            }
			
            if(changeOpacity){
                $(item).fadeIn(speed);
                $(item).customFadeIn(speed);
            }
			
            if(adjustImage)
                item = $(item).children('img:first');
			
            var height = $(item).height();
            $(currentParent).children('#slides').animate({
                'height': height + 'px'
            }, speed);
			
            var button = $(currentParent).children('#slides').children('#buttonPreview');
            $(button).animate({
                'margin-top': ((height / 2) - ($(button).height() / 2)) + 'px'
            }, speed );
            button = $(currentParent).children('#slides').children('#buttonNext');
            $(button).animate({
                'margin-top': ((height / 2) - ($(button).height() / 2)) + 'px'
            }, speed );
			
            rotateMin(index);
        }
        
        function setChilds(element, attribute){
            $(element).children('*').each(function(index, element) {
                $(this).css(attribute);
                setChilds(this, attribute);
            });
        }
		
        function rotateMin(position){	
            index_min = checkIndex(position);		
            var item = $(currentParent).children('#slides_min')
            .children('ul');
			
            $(item).animate({
                'margin-left': (((width / 2) - (index_min * getMinItemWidth()) - (getMinItemWidth() / 2)) + 'px')
            }, speed );
        }
		
        function getMinItemWidth(){
            return $(currentParent).children('#slides_min')
            .children('ul')
            .children('li:first')
            .width();
        }
		
        var buttonPrev = $(this).children('#slides').children('#buttonPreview');
        var buttonNext = $(this).children('#slides').children('#buttonNext');
        $(buttonPrev).addClass(options.arrowPreview);
        $(buttonNext).addClass(options.arrowNext);
        $(buttonNext).css({
            'margin-left': (width - 19)
        });
        $(buttonPrev).click(function(e) {
            preChanged = true;
            rotateImage(-1, -1);
        });
        $(buttonNext).click(function(e) {
            preChanged = true;
            rotateImage(1, -1);
        });
		
        buttonPrev = $(this).children('#slides_min').children('#buttonPreview');
        buttonNext = $(this).children('#slides_min').children('#buttonNext');
        $(buttonPrev).addClass('arrow_nav1');
        $(buttonNext).addClass('arrow_nav2');
        $(buttonNext).css({
            'margin-left': (width - $(buttonNext).width())
        });
        $(buttonPrev).click(function(e) {
            rotateMin(index_min - 5);
        });
        $(buttonNext).click(function(e) {
            rotateMin(index_min + 5);
        });
		
        var plays = $(this).children('#slides').children('#play');
        var stops = $(this).children('#slides').children('#stop');
        $(plays).css({
            'display':'none'
        });
        $(plays).addClass('play');
        $(stops).addClass('stop');
        $(stops).css({
            'margin-left': (width - 16)
        });
        $(plays).css({
            'margin-left': (width - 16)
        });
		
        $(stops).click(function(e) {
            changeActive = false;
            $(stops).css({
                'display':'none'
            });
            $(plays).css({
                'display':'block'
            });
        });
        $(plays).click(function(e) {
            changeActive = true;
            $(stops).css({
                'display':'block'
            });
            $(plays).css({
                'display':'none'
            });
        });
    }
	
    /*Custom animations*/
	
    /*Cambia el tamaño de un objeto hasta quedarse quieto*/
    $.fn.coloide = function(options){
		
        var defaults = {	
            recursive: false
        };
		
        var options = $.extend(defaults, options);
		
        var X= $(this).width();
        var Y= $(this).height();
        var orX= X;
        var orY= Y;
        var orColor = $(this).css('color');
        var increment = 10;
        var posIncr = 4;
        var posDecr = 7;
		
        if(options.recursive){
            $(this).children('*').each(function(index, element) {
                $(this).coloide();
            });
        }
		
        $(this).mouseenter(function(){
            orX+= increment;
            orY+= increment;
            var dir = true;
            while(orY > Y || orX > X){
                $(this).animate({
                    width: orX + "px",
                    color: 'blue'
                /*height: orY + "px"*/
                }, 50);
			 
                if(dir){
                    orX+= posIncr;
                    orY+= posIncr;
                }else{
                    orX-= posDecr;
                    orY-= posDecr;
                }
                dir = !dir;
            }
        });
		
        $(this).mouseleave(function(e) {
            $(this).animate({
                color: orColor
            }, 50 );
        });
    }
	
    /*Crea 6 cuadros de seleccion incrementables*/
    $.fn.boxGrid = function(options){
        function sorts(current, element, attrib){
            switch ($(element).attr('id'))
            {
                case '2':
                case '3':
                case '5':
                case '6':
                    attrib = 'name';
                    break;
                default:
                    attrib = 'id';
                    break;
            }
            var elements = $(current).children('li');
            elements.sort(function(a, b) {
                var compA = $(a).attr(attrib).toUpperCase();
                var compB = $(b).attr(attrib).toUpperCase();
                return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
            });
            $(current).append(elements);	
        }
		
        function renameAll(parent){
            var count = 0;
            var numbers = {
                1:3,
                2:2,
                3:1,
                4:6,
                5:5,
                6:4
            };
            var styles = {
                1:'right',
                2:'left',
                3:'left',
                4:'right',
                5:'left',
                6:'left'
            };
            $(parent).children('li').each(function() {
                ++count;
                if(count > 6)
                    return;
                $(this).attr('name', count);
                $(this).attr('id', numbers[count]);
                $(this).addClass('boxgrid-small-box');
                $(this).css({
                    'float': styles[count]
                });
            });
        }
		
        function minimizeAll(parent){
			
            this.defaults = {	
                width: '100%',
                height: '500px'
            }; 
		
            $(parent).children('li').each(function() {
				
                $(this).children('div:eq(0)').animate({
                    width: "100%",
                    height: "100%",
                    display: "block",
                    opacity: "1"
                }, 500 );
                $(this).children('div:eq(1)').animate({
                    width: "0px",
                    height: "0px",
                    display: "none",
                    opacity: "0"
                }, 500 );
				 
                $(this).animate({
                    width: ((orX) + "px"),
                    height: ((orY) + "px")
                }, 500 );
            });
        }
		
        //$(this).height('300px');
        //$(this).width('300px');
        var orX= $(this).width() / 3;
        var orY= $(this).height() / 3;
        var currentParent = $(this);
        renameAll(currentParent);
        minimizeAll(currentParent);
        $(this).height('auto');
		
        this.each(function(){
            $(this).children('li').each(function() {
                $(this).click(function(e) {
                    sorts(currentParent, $(this));
                    minimizeAll(currentParent);
				 
                    $(this).children('div:eq(0)').animate({
                        width: "0px",
                        height: "0px",
                        display: "none",
                        opacity: "0"
                    }, 500 );
                    $(this).children('div:eq(1)').animate({
                        width: "100%",
                        height: "100%",
                        display: "block",
                        opacity: "1"
                    }, 500 );
				 
                    $(this).animate({
                        width: ((orX * 2) + "px"),
                        height: ((orY * 2) + "px")
                    }, 500 );
                });
            });
        });
    }
	
    /*Aumenta de tamaño a un objeto*/
    $.fn.giants = function(options){
        var toggle = true;
        var orX= $(this).width();
        var orY= $(this).height();
		
        this.each(function(){
            $(this).click(function(){
                if(toggle){
                    $(this).animate({
                        width: ((orX * 3) + "px"),
                        height: ((orY * 3) + "px"),
                        opacity: 0.4,
                        marginLeft: "50px",
                        fontSize: "3em",
                        borderWidth: "10px"
                    }, 1500 );
                }else{
                    $(this).animate({
                        width: (orX + "px"),
                        height: (orY + "px"),
                        opacity: 1,
                        marginLeft: "0px",
                        fontSize: "3em",
                        borderWidth: "10px"
                    }, 1500 );
                }
                toggle=!toggle;
            });
        });
    }
	
	
    /*Cambia de tamaño una barra de titulo expandiendola*/
    $.fn.titleMorph = function(options){
        var defaults = {	
            expandWidth: 1000,
            collapseWidth: 500
        };
		
        var options = $.extend(defaults, options);
        var orX=$(this).width();
        var orY=$(this).height();
        contraer($(this));
        $(this).css({
            'margin-left':'auto',
            'margin-right':'auto'
        });
		
        this.each(function(){
            $(this).mouseenter(function(){
                expandir($(this));
            });
            $(this).mouseleave(function(){
                contraer($(this));
            });
        });
		
        function expandir(object){
            $(object).stop();
            $(object).animate({
                width: options.expandWidth + "px",
                //marginRight: "0%",
                'border-top-right-radius': "20px",
                'border-top-left-radius': "0px",
                'border-bottom-right-radius': "0px",
                'border-bottom-left-radius': "20px"
            }, 1000 );
        }
		
        function contraer(object){
            $(object).stop();
            $(object).animate({
                width: options.collapseWidth + "px",
                //'margin-right': "50%",
                'border-top-right-radius': "20px",
                'border-top-left-radius': "20px",
                'border-bottom-right-radius': "20px",
                'border-bottom-left-radius': "20px"
            }, 1000 );
        }
    }
	
    /*Un cuadro que muestra informacion acerca de una imagen*/
    $.fn.boxShield = function(options){
        var orX=$(this).width();
        var orY=$(this).height();
		
        /*
		var image = $(this).children('img#background');
        $(image).css({
            'display':'none'
        });
        $(this).css({
            'background-image':'url(' + $(image).attr('src') + ')'
        });
		*/
		
		
        this.each(function(){
            var element =  $(this).children('div#subShield');
            $(element).css({
                'top': '0px',
                'left': '0px'
            });
            $(this).mouseenter(function(){
                $(element).stop();
                $(element).animate({
                    left: orX + 'px',
                    top: orY + 'px'
                }, 500 );
            });
            $(this).mouseleave(function(){
                $(element).stop();
                $(element).animate({
                    left: '0px',
                    top: '0px'
                }, 500 );
            });
        });
    }
	
    $.fn.acordeon = function (options){
        var defaults = {	
            speed: 500,
            width: 300,
            height: 200
        };
		
        var options = $.extend(defaults, options);  
        var currentObject = this;
        collapseAll(false);
        var toggling = false;
        $(this).width(options.width);
		
        $(this).children('.acordeon-container-title').each(function(index, element) {
            $(this).click(function(e) {
                collapseAll(true);
            });
        });
        $(this).children('.acordeon-container').each(function(index, element) {
            $(this).children('.acordeon-section').each(function(index, element) {
                $(this).children('.acordeon-title:first').click(function(e){					
                    if(toggling) 
                        return;
                    collapseAll(true);
                    $(this).addClass('acordeon-title-select');
                    //$(this).stop();
                    toggling = true;
                    var acodeonContent=$(this).parent().children('.acordeon-content');
                    $(acodeonContent).slideToggle(options.speed, function(){
                        toggling = false;
                    });
                    $(currentObject).stop();
                    $(currentObject).animate({
                        'margin-top': '-' + options.height + 'px'
                    }, options.speed);
                });
            });
        });
		
        function collapseAll(animate){	
            $(currentObject).stop();
            $(currentObject).animate({
                'margin-top': '0px'
            }, options.speed);
		  
            $(currentObject).children('.acordeon-container').each(function(index, element) {
                $(this).children('.acordeon-section').each(function(index, element) {
                    var items = $(this).children('.acordeon-content:first');
                    $(this).children('.acordeon-title:first').removeClass('acordeon-title-select');
                    //$(items).stop();
                    if(animate){
                        $(items).slideUp(options.speed);
                    }else{
                        $(items).slideUp(0);
                    }
                });
            });
        }
    }
	
	
    $.fn.sectioner = function (options){
        var defaults = {	
            height: 500,
            width: 650,
            speed:1000,
            initialIndex:0
        };
		
        var options = $.extend(defaults, options);  
		
        $(this).height(options.height);
        var currentObject = this;
        var counter = 0;
        $(this).children('.sectioner-sections:first').children('div').each(function(index, element) {
            $(this).attr('name', counter);
            $(this).click(function(e) {
                slideToIndex($(this).attr('name'), this);
            });
            ++counter;
        });
        counter = 0;
        $(this).children('.sectioner-contents:first').height(options.height);
        $(this).children('.sectioner-contents:first').children('.sectioner-contents-container:first').children('div').each(function(index, element) {
            $(this).width(options.width);
            $(this).height(options.height);
            $(this).attr('name', counter);
            ++counter;
        });
		
        slideToIndex(options.initialIndex, $(this).children('.sectioner-sections:first').children('div:eq(' + options.initialIndex + ')'));
		
        function resetStyles(){		
            $(currentObject).children('.sectioner-sections:first').children('div').each(function(index, element) {
                $(this).removeClass('sectioner-section-select');
            });
        }
		
        function slideToIndex(index, element){
            resetStyles();
            $(element).addClass('sectioner-section-select');
				
            var value = parseInt(index) * options.height;
            //alert(value);
            var element = $(currentObject).children('.sectioner-contents:first').children('.sectioner-contents-container:first');
            $(element).stop();
            $(element).animate({
                'margin-top' : '-' + value + 'px'
            }, options.speed);
        }
    }
})(jQuery);