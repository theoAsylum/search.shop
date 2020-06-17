function FPShopSearchStart(arParams)
{
    var _this = this;
    
    this.arParams = {
        'CONTAINER_RESULT': arParams.CONTAINER_RESULT,
		'INPUT': arParams.INPUT,
	};

	this.Init = function()
	{
		this.CONTAINER = BX.findChild(document, {'class':arParams.INPUT}, true);
        var init = false;
		BX.bind(this.CONTAINER, 'focus', function() {
            if(!init){
                BX.ajax.post(
                    window.location.toString(),
                    {
                        'ajax_start':'y',
                    }
                );
                init = true;
            }
        });
	};
	BX.ready(function (){_this.Init(arParams)});
}

function FPShopSearch(arParams)
{
	var _this = this;
    
    this.arParams = {
		'CONTAINER_RESULT': arParams.CONTAINER_RESULT,
		'INPUT': arParams.INPUT,
		'MIN_QUERY_LEN': parseInt(arParams.MIN_QUERY_LEN),
		'RESULT_COUNT': parseInt(arParams.RESULT_COUNT)
	};
    if(arParams.MIN_QUERY_LEN <= 0)
		arParams.MIN_QUERY_LEN = 1;
    this.ITEMS = arParams.ITEMS;
    
    this.startText = '';
	this.running = false;
	this.currentRow = -1;
	this.RESULT = null;
	this.CONTAINER = null;
	this.INPUT = null;
    
	this.ShowResult = function(result)
	{
        if(BX.type.isElementNode(result))
		{
            var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'search-list'}, true);
            if(tbl) tbl.parentNode.removeChild(tbl);
            _this.RESULT.appendChild(result);
            var pos = _this.adjustResultNode();
		}
        _this.RESULT.style.display = _this.RESULT.firstChild.innerHTML !== '' ? 'block' : 'none';
	};

	this.onKeyPress = function(keyCode)
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'search-list'}, true);
		if(!tbl)
			return false;

		var i;
		var cnt = tbl.rows.length;

		switch (keyCode)
		{
		case 27: // escape key - close search div
			_this.RESULT.style.display = 'none';
			_this.currentRow = -1;
			_this.UnSelectAll();
		return true;

		case 40: // down key - navigate down on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var first = -1;
			for(i = 0; i < cnt; i++)
			{
                if(first == -1)
                    first = i;

                if(_this.currentRow < i)
                {
                    _this.currentRow = i;
                    break;
                }
                else if(tbl.rows[i].classList.contains("search-list__item-selected"))
                {
                    tbl.rows[i].classList.remove("search-list__item-selected");
                }
			}

			if(i == cnt && _this.currentRow != i)
				_this.currentRow = first;

			tbl.rows[_this.currentRow].classList.add("search-list__item-selected");
		return true;

		case 38: // up key - navigate up on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var last = -1;
			for(i = cnt-1; i >= 0; i--)
			{
                if(last == -1)
                    last = i;

                if(_this.currentRow > i)
                {
                    _this.currentRow = i;
                    break;
                }
                else if(tbl.rows[i].classList.contains("search-list__item-selected"))
                {
                    tbl.rows[i].classList.remove("search-list__item-selected");
                }
			}

			if(i < 0 && _this.currentRow != i)
				_this.currentRow = last;

			tbl.rows[_this.currentRow].classList.add("search-list__item-selected")
		return true;

		case 13: // enter key - choose current search result
            if(_this.currentRow == -1){
                if(_this.RESULT.firstChild.innerHTML){
                    
                    _this.currentRow = 0;
                    
                }else{
                    BX.ajax.post(
                        window.location.toString(),
                        {
                            'ajax_result':'y',
                            'how':3,
                            'name':_this.INPUT.value
                        },
                        function(result)
                        {
                            if(BX.type.isString(result))
                            {
                                var res = document.createElement("DIV");
                                res.innerHTML = result.trim();
                                document.body.appendChild(res.firstChild);
                            }
                        }
                    );
                    
                }
            }
			if(_this.RESULT.style.display == 'block')
			{
				for(i = 0; i < cnt; i++)
				{
					if(_this.currentRow == i)
					{
                        var p = BX.findChild(tbl.rows[i], {'tag':'p'}, true);
                        if(p)
                        {
                            var how = p.getAttribute('attr-how');
                            var name = p.getAttribute('attr-name');
                            _this.INPUT.value = p.innerHTML;
                            
                            BX.ajax.post(
                                window.location.toString(),
                                {
                                    'ajax_result':'y',
                                    'how':how,
                                    'name':name
                                },
                                function(result)
                                {
                                    if(BX.type.isString(result))
                                    {
                                        var res = document.createElement("DIV");
                                        res.innerHTML = result.trim();
                                        document.body.appendChild(res.firstChild);
                                    }
                                }
                            );
                        }
					}
				}
			}
		return true;
		}

		return false;
	};
    
	this.onClickSearch = function(e)
	{
        if(!e)
			e = window.event;
        
        e.preventDefault();
        
        var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'search-list'}, true);
		if(!tbl)
			return false;
        
        if(_this.currentRow == -1){
            if(_this.RESULT.firstChild.innerHTML){

                _this.currentRow = 0;

            }else{
                BX.ajax.post(
                    window.location.toString(),
                    {
                        'ajax_result':'y',
                        'how':3,
                        'name':_this.INPUT.value
                    },
                    function(result)
                    {
                        if(BX.type.isString(result))
                        {
                            var res = document.createElement("DIV");
                            res.innerHTML = result.trim();
                            document.body.appendChild(res.firstChild);
                        }
                    }
                );
                return true;
            }
        }
        var p = BX.findChild(tbl.rows[_this.currentRow], {'tag':'p'}, true);
        if(p)
        {
            var how = p.getAttribute('attr-how');
            var name = p.getAttribute('attr-name');
            _this.INPUT.value = p.innerHTML;

            BX.ajax.post(
                window.location.toString(),
                {
                    'ajax_result':'y',
                    'how':how,
                    'name':name
                },
                function(result)
                {
                    if(BX.type.isString(result))
                    {
                        var res = document.createElement("DIV");
                        res.innerHTML = result.trim();
                        document.body.appendChild(res.firstChild);
                    }
                }
            );
            return true;
        }
        return false;
    };
    
	this.onTimeout = function()
	{
		_this.onChange(function(){
			setTimeout(_this.onTimeout, 500);
		});
	};

	this.onChange = function(callback)
	{
		if (_this.running)
			return;
		_this.running = true;
		_this.INPUT.value = _this.INPUT.value.toLowerCase().replace('https://', '');
		if(_this.INPUT.value != _this.oldValue && _this.INPUT.value != _this.startText)
		{
			_this.oldValue = _this.INPUT.value;
			if(_this.INPUT.value.length >= _this.arParams.MIN_QUERY_LEN)
			{
                this.BUTTON.removeAttribute('disabled');
                var result_table = document.createElement("table");
                result_table.className = 'search-list';
                
                var result = '', 
                    key, 
                    search = _this.INPUT.value,
                    element_td,
                    element_p,
                    i = 1;
                
                for (key in this.ITEMS) {
                    if (-1 < this.ITEMS[key].SITE.indexOf(search)) {
                        
                        element_td = result_table.appendChild(document.createElement("tr"));
                        element_td.className = "search-list__item";
                        
                        element_p = element_td.appendChild(document.createElement("p"));
                        element_p.setAttribute("attr-how", this.ITEMS[key].HOW);
                        if(this.ITEMS[key].NAME != undefined) element_p.setAttribute("attr-name", this.ITEMS[key].NAME);
                        element_p.className = "search-list__text";
                        element_p.innerHTML = this.ITEMS[key].SITE;
                        
                        i += 1;
                        if(i > arParams.RESULT_COUNT) break;
                    }
                }
                
                
                _this.ShowResult(result_table);
                _this.currentRow = -1;
                _this.EnableMouseEvents();
                if (!!callback)
                    callback();
                _this.running = false;
                return;
                
			}
			else
			{
				_this.RESULT.style.display = 'none';
				_this.currentRow = -1;
				_this.UnSelectAll();
			}
		}
		if (!!callback)
			callback();
		_this.running = false;
	};

	this.onScroll = function ()
	{
		if(BX.type.isElementNode(_this.RESULT)
			&& _this.RESULT.style.display !== "none"
			&& _this.RESULT.innerHTML !== ''
		)
		{
			_this.adjustResultNode();
		}
	};

	this.UnSelectAll = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'search-list'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				tbl.rows[i].classList.remove("search-list__item-selected");
		}
	};

	this.EnableMouseEvents = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'search-list'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++){
                tbl.rows[i].id = 'row_' + i;
                tbl.rows[i].onclick = function (e) {
                    var shop = this.firstChild;
                    var how = shop.getAttribute('attr-how');
                    var name = shop.getAttribute('attr-name');
                    _this.INPUT.value = shop.innerHTML;
                    _this.onChange();
                    BX.ajax.post(
                        window.location.toString(),
                        {
                            'ajax_result':'y',
                            'how':how,
                            'name':name
                        },
						function(result)
						{
                            if(BX.type.isString(result))
                            {
                                var res = document.createElement("DIV");
                                res.innerHTML = result.trim();
                                document.body.appendChild(res.firstChild);
                            }
						}
                    );
                };
                tbl.rows[i].onmouseover = function (e) {
                    _this.UnSelectAll();
                    this.classList.add("search-list__item-selected");
                    _this.currentRow = this.id.substr(4);
                };
                tbl.rows[i].onmouseout = function (e) {
                    this.classList.remove("search-list__item-selected");
                    _this.currentRow = -1;
                };
            }
		}
	};

	this.onFocusLost = function(hide)
	{
		setTimeout(function(){_this.RESULT.style.display = 'none';}, 250);
	};

	this.onFocusGain = function()
	{
        if(_this.RESULT.firstChild)
             _this.ShowResult();
	};

	this.onKeyDown = function(e)
	{
		if(!e)
			e = window.event;

		if (_this.RESULT.style.display == 'block')
		{
			if(_this.onKeyPress(e.keyCode))
				return BX.PreventDefault(e);
		}
        if (_this.INPUT.value && e.keyCode == 13)
        {
            if(_this.onKeyPress(e.keyCode))
                return BX.PreventDefault(e);
        }
	};

	this.adjustResultNode = function()
	{
		if(!(BX.type.isElementNode(_this.RESULT)
			&& BX.type.isElementNode(_this.CONTAINER))
		)
		{
			return { top: 0, right: 0, bottom: 0, left: 0, width: 0, height: 0 };
		}

		var pos = BX.pos(_this.CONTAINER);

		_this.RESULT.style.position = 'absolute';
		_this.RESULT.style.top = (pos.bottom + 2) + 'px';
		_this.RESULT.style.left = pos.left + 'px';
		_this.RESULT.style.width = pos.width + 'px';

		return pos;
	};

	this._onContainerLayoutChange = function()
	{
		if(BX.type.isElementNode(_this.RESULT)
			&& _this.RESULT.style.display !== "none"
			&& _this.RESULT.innerHTML !== ''
		)
		{
			_this.adjustResultNode();
		}
	};
	this.Init = function()
	{
		this.CONTAINER = BX.findChild(document, {'class':this.arParams.CONTAINER_RESULT}, true);
		BX.addCustomEvent(this.CONTAINER, "OnNodeLayoutChange", this._onContainerLayoutChange);
		this.RESULT = document.body.appendChild(document.createElement("DIV"));
		this.RESULT.className = 'search-list';
        this.INPUT = BX.findChild(document, {'class':this.arParams.INPUT}, true);
        this.BUTTON = BX.findChild(document, {'class':arParams.BUTTON}, true);
        BX.bind(this.BUTTON, 'click', function() {_this.onClickSearch()});
		this.startText = this.oldValue = this.INPUT.value;
		BX.bind(this.INPUT, 'focus', function() {_this.onFocusGain()});
		BX.bind(this.INPUT, 'blur', function() {_this.onFocusLost()});
		this.INPUT.onkeydown = this.onKeyDown;

		BX.bind(this.INPUT, 'bxchange', function() {_this.onChange()});

		var fixedParent = BX.findParent(this.CONTAINER, BX.is_fixed);
		if(BX.type.isElementNode(fixedParent))
		{
			BX.bind(window, 'scroll', BX.throttle(this.onScroll, 100, this));
		}
	};
	BX.ready(function (){_this.Init(arParams)});
}