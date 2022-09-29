@echo off
SET JSCompPath=C:\data\svn\dev\CPP\JSCompressor\Debug
%JSCompPath%\JSCompressor.exe dataIslands.js > _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe DOM.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe events.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe navigator.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe ncDOMParser.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe ncNotDOMGetter.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe ncXmlHttpRequest.js >> _pToolsnavigatorCompat.js
%JSCompPath%\JSCompressor.exe XSLT.js >> _pToolsnavigatorCompat.js
