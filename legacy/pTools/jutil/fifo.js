function fifo()
{
	/* members */
	this._fifosize = 0;
	this._fifotable = new Array();
	
	/* public functions */
	this.destroy = fifo_destroy;
	this.push_back = fifo_push_back;
	this.pop_front = fifo_pop_front;
	this.insert = fifo_insert;
	this.size = fifo_size;
	this.clear = fifo_clear;
	this.getElement = fifo_getElement;
	this.removeElement = fifo_removeElement;
}

function fifo_destroy()
{
	this.clear();
	delete this_fifotable;
}

function fifo_insert(wantedIdx, el)
{
  if (wantedIdx >= this._fifosize)
	  this.push_back(el);
	else
	{
		for (var i=this._fifosize-1; i >= wantedIdx; i--)
		{
			this._fifotable[i+1] = this._fifotable[i];
		}
		this._fifotable[wantedIdx] = el;
		this._fifosize++;
	}
}

function fifo_push_back(el)
{
  var idx = this._fifosize;
	this._fifotable[idx] = el;
	this._fifosize++;
}

function fifo_pop_front()
{
	if (this._fifosize == 0)
	  return null;
	
	var retObj = this._fifotable[0];
	for (var i=0; i < this._fifosize-1; i++)
	{
		this._fifotable[i] = this._fifotable[i+1];
	}
	this._fifotable[this._fifosize-1]=null;
	this._fifosize--;
	return retObj;
}

function fifo_size()
{
	return this._fifosize;
}

function fifo_clear()
{
	for (var i=0; i < this._fifosize; i++)
	{
		var el = this._fifotable[i];
		this._fifotable[i]=null;
		delete el;
	}
	this._fifosize=0;
}

function fifo_getElement(idx)
{
  if (idx < this._fifosize)
	  return this._fifotable[idx];
  else
	  return null;
}

function fifo_removeElement(idx)
{
  if (idx >= this._fifosize)
	  return null;
		
	var retObj = this._fifotable[idx];
	for (var i=idx; i < this._fifosize-1; i++)
	{
		this._fifotable[i] = this._fifotable[i+1];
	}
	this._fifotable[this._fifosize-1]=null;
	this._fifosize--;
	return retObj;
}