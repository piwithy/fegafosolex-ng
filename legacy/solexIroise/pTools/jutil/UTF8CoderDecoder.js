
/* constants */
var TxtConv_MASKBITS     = 0x3F;
var TxtConv_MASKBYTE     = 0x80;
var TxtConv_MASK2BYTES   = 0xC0;
var TxtConv_MASK3BYTES   = 0xE0;
var TxtConv_MASK4BYTES   = 0xF0;
var TxtConv_MASK5BYTES   = 0xF8;
var TxtConv_MASK6BYTES   = 0xFC;

var UTF8_HEADER_CODE = new Array(0xEF, 0xBB, 0xBF); /* in microsoft files only */
var UCS2_BOM_BIGENDIAN = new Array (0xFE, 0xFF);		 /* mandatory for ucs2 */
var UCS2_BOM_LITTLEENDIAN = new Array (0xFF, 0xFE);  /* mandatory for ucs2 */

/*
// j'ai mis "WithCharacterLoose" c'est pour etre bien clair que cette transformation n'est theoriquement pas possible
int Utf8ToAnsiWithCharacterLoose(char* utf8in, char** destAnsi)
{
	// 1: conversion to unicode
	void* unicode2 = NULL;
	int unicode2len = 0;
	Utf8ToUnicode2Bytes(utf8in, 0, &unicode2, &unicode2len);

	// 2: conversion to ansi 
	Unicode2Bytes* unicode = (Unicode2Bytes*) unicode2;
	char* buf = (char*) malloc(unicode2len/2);
	for (int i=1; i < unicode2len/2; i++)
	{
		buf[i-1] = unicode[i].l;
	}
	free(unicode2);
	*destAnsi = buf;
	return strlen(buf);
}

// search for a current utf8 character in a maybe-truncated buffer
int Utf8GetCurrentCharacterInfo(void* aBufIn, int aBufInSize, char* carlen, int* isComplete)
{
	*carlen=0;
	*isComplete=0;
	unsigned char* utf8inUnsigned = (unsigned char*) aBufIn;
	if(utf8inUnsigned[0] < 0x80)
	{
		*carlen=1;
		*isComplete=1;//on va le tester plus bas
	}
	else if((utf8inUnsigned[0] & TxtConv_MASK3BYTES) == TxtConv_MASK2BYTES)
	{
		*carlen=2;
		*isComplete=1;//on va le tester plus bas
	}
	else if((utf8inUnsigned[0] & TxtConv_MASK4BYTES) == TxtConv_MASK3BYTES)
	{
		*carlen=3;
		*isComplete=1;//on va le tester plus bas
	}
	else
	{
		// bad utf8 string character
		BSP_SystemAssertGlbFunc(0);
	}
	if (*carlen > aBufInSize) *isComplete=0;
	return *carlen; // on retourne la longueur utile du buffer
}

// search for last utf8 character in a maybe-truncated buffer
int Utf8GetLastCharacterInfo(void* aBufIn, int aBufInSize, char** startpos, char* carlen, int* isComplete)
{
	*carlen=0;
	*startpos=0;
	*isComplete=0;
	char subcarlen=0;
	int subIsComplete=0;
	for(int i=0; i < aBufInSize;)
	{
		*startpos = (char*)aBufIn+i;
		
		Utf8GetCurrentCharacterInfo(*startpos, aBufInSize-i, &subcarlen, &subIsComplete);
		*isComplete = subIsComplete;
		*carlen = subcarlen;
		if (!subIsComplete) break; // problem
		i+=subcarlen;
	}

	return (*isComplete) ? aBufInSize : 0; // on devrait retourner la longueur utile du buffer. dans le cas non complet c'est encore TODO
}
*/
function AnsiToUtf8(srcAnsi)
{
	/*dstUtf8*/
	// 1: conversion to unicode
	var unicode2BufferAndLen = AnsiToUnicode2Bytes(srcAnsi, 0);

	// 2: conversion to utf8
	return Unicode2BytesToUtf8(unicode2BufferAndLen);
}


function AnsiToUnicode2Bytes(srcAnsi, BigEndian1)
{
	var unicodeLen = 2*(srcAnsi.length+1);
	var unicode = new Array();

	if (BigEndian1)
	{
		for (var i=0; i < UCS2_BOM_BIGENDIAN.length; i++)
			unicode[unicode.length] = UCS2_BOM_BIGENDIAN[i];
	}
	else
	{
		for (var i=0; i < UCS2_BOM_LITTLEENDIAN.length; i++)
			unicode[unicode.length] = UCS2_BOM_LITTLEENDIAN[i];
	}

	for (var i=0; i < srcAnsi.length; i++)
	{
		if (BigEndian1)
		{
			unicode[unicode.length] = srcAnsi.charCodeAt(i);
			unicode[unicode.length] = 0x00;
		}
		else
		{
			unicode[unicode.length] = 0x00;
			unicode[unicode.length] = srcAnsi.charCodeAt(i);
		}
	}
	return new Array(unicode, unicodeLen);
}

function Unicode2BytesToUtf8(unicode2BufferAndLen)
{
	var buf = unicode2BufferAndLen[0];
	var buflen = unicode2BufferAndLen[1];
	
	var BigEndian=0;
	var bypassBOMoffset = 0;
	if ((buf[0] == UCS2_BOM_BIGENDIAN[0]) && (buf[1] == UCS2_BOM_BIGENDIAN[1]))
	{
		BigEndian=1;
		bypassBOMoffset+=2;
	}
	else if ((buf[0] == UCS2_BOM_LITTLEENDIAN[0]) && (buf[1] == UCS2_BOM_LITTLEENDIAN[1]))
	{
		BigEndian=0;
		bypassBOMoffset+=2;
	}
	else
	{
		// ce n'est pas de l'UCS2, le BOM est obligatoire !!
		alert("assert(0) Unicode2BytesToUtf8");
		return "";
	}

	var utf8 = "";
	var offset = 0;
	for(var i=bypassBOMoffset; i < buflen; i+=2)
	{
		var liShort=0;
		if (BigEndian)
		{
			liShort = (buf[i+1]<<8) + buf[i];
		}
		else
		{
			liShort = (buf[i]<<8) + buf[i+1];
		}
		// 0xxxxxxx
		if(liShort < 0x80)
		{
			utf8 += String.fromCharCode(liShort);
		}
		// 110xxxxx 10xxxxxx
		else if(liShort < 0x800)
		{
			 utf8 += String.fromCharCode((TxtConv_MASK2BYTES | (liShort >> 6)));
			 utf8 += String.fromCharCode((TxtConv_MASKBYTE | (liShort & TxtConv_MASKBITS)));
		}
		// 1110xxxx 10xxxxxx 10xxxxxx
		else if(liShort < 0x10000)
		{
			 utf8 += String.fromCharCode((TxtConv_MASK3BYTES | (liShort >> 12)));
			 utf8 += String.fromCharCode((TxtConv_MASKBYTE | ((liShort >> 6) & TxtConv_MASKBITS)));
			 utf8 += String.fromCharCode((TxtConv_MASKBYTE | (liShort & TxtConv_MASKBITS)));
		}
	}
	return utf8;
}
/*
int Utf8ToUnicode2Bytes(char* utf8in, int BigEndian1, void** aBufOut, int* aBufOutSize)
{
	unsigned char* utf8inUnsigned = (unsigned char*) utf8in;
	Unicode2Bytes* unicode = (Unicode2Bytes*) malloc(2*(strlen(utf8in)+1)+2); // cas le pire...
	int offset=0;

	if (BigEndian1)
	{
		memcpy(unicode, UCS2_BOM_BIGENDIAN, sizeof(UCS2_BOM_BIGENDIAN));
	}
	else
	{
		memcpy(unicode, UCS2_BOM_LITTLEENDIAN, sizeof(UCS2_BOM_LITTLEENDIAN));
	}
	offset++;

	for(int i=0; i < strlen(utf8in)+1;)
	{
		unsigned short ch;

		// 0xxxxxxx
		if(utf8inUnsigned[i] < 0x80)
		{
			 ch = utf8inUnsigned[i];
			 i += 1;
		}
		// 110xxxxx 10xxxxxx
		else if((utf8inUnsigned[i] & TxtConv_MASK3BYTES) == TxtConv_MASK2BYTES)
		{
			 ch = ((utf8inUnsigned[i] & 0x1F) << 6) | (utf8inUnsigned[i+1] & TxtConv_MASKBITS);
			 i += 2;
		}
		// 1110xxxx 10xxxxxx 10xxxxxx
		else if((utf8inUnsigned[i] & TxtConv_MASK4BYTES) == TxtConv_MASK3BYTES)
		{
			 ch = ((utf8inUnsigned[i] & 0x0F) << 12) | (
						 (utf8inUnsigned[i+1] & TxtConv_MASKBITS) << 6)
						| (utf8inUnsigned[i+2] & TxtConv_MASKBITS);
			 i += 3;
		}
		else
		{
			// bad utf8 string
			BSP_SystemAssertGlbFunc(0); 
			ch=0;
			i++;
		}

		Unicode2Bytes c;
		if (BigEndian1)
		{
			c.l = (ch & 0xFF00)>>8;
			c.h = ch & 0xFF;
		}
		else
		{
			c.l = ch & 0xFF;
			c.h = (ch & 0xFF00)>>8;
		}

		unicode[offset++] = c;
	}
	*aBufOut = unicode;
	*aBufOutSize = 2*offset;
	return *aBufOutSize;
}

int AllISOLatinChars(char* aiszText)
{
	unsigned char* p = (unsigned char*) aiszText;
	while (*p)
	{
		if (*p > 0x80)
		{
			return 0;
			break;
		}
		p++;
	}
	return -1;
}
*/