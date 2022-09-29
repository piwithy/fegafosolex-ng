function jExpression()
{
  /* members */
  this.HTMLExpression = "";
  
  /* functions */
  this.parse = jExpression_parse;
  this.getStartToken = jExpression_getStartToken;
  this.getEndToken = jExpression_getEndToken;
}

function jExpression_parse(expressionNode)
{
  for (var c=0; c < expressionNode.childNodes.length; c++)
  {
    var child = expressionNode.childNodes[c];
    if (ncNodeTag(child)==null)
      continue; /* not a node */
    if (ncNodeTag(child)=="html")
    {
      var dumpWithHtmlTags = ncNodeDump(child);
      this.HTMLExpression = dumpWithHtmlTags.substring(6, dumpWithHtmlTags.length-7);
    }
    else
    {
      alert("jExpression::parse Expected 'html', got '"+ncNodeTag(child)+"'");
      return 0;
    }
  }
  return 1;
}

function jExpression_getStartToken()
{
  var pos = this.HTMLExpression.indexOf('#text');
  var startTk = this.HTMLExpression.substring(0, pos);
  return startTk;
}

function jExpression_getEndToken()
{
  var pos = this.HTMLExpression.indexOf('#text');
  var endTk = this.HTMLExpression.substring(pos+5);
  return endTk;
}