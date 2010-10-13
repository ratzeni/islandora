<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:variable name="BASEURL">
		 	<xsl:value-of select="$baseUrl"/>
	</xsl:variable>
	<xsl:variable name="PATH">
		 	<xsl:value-of select="$path"/>
	</xsl:variable>
<xsl:template match="/">

<div><table cellspacing="3" cellpadding="3"><tbody>
<tr><th colspan="4"><h3>MAG</h3></th></tr>


<xsl:for-each select="/mag/*">
	<xsl:variable name="FULLFIELD" select="name()"/>
	<xsl:variable name="FIELD" select="name()"/>
	<xsl:variable name="DATA" select="text()"/>
	
	<xsl:if test="$FIELD = 'audio'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<xsl:if test="name() != 'proxies'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if>
	</xsl:for-each>
	<xsl:for-each select="*/*">
		<xsl:if test="name() != 'format'"><xsl:if test="name() != 'audio_metrics'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if></xsl:if>
	</xsl:for-each>
	<xsl:for-each select="*/*/*">
		<xsl:if test="name() != 'format'"><xsl:if test="name() != 'audio_metrics'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if></xsl:if>
	</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'video'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<xsl:if test="name() != 'proxies'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if>
	</xsl:for-each>
	<xsl:for-each select="*/*">
		<xsl:if test="name() != 'format'"><xsl:if test="name() != 'video_metrics'"><xsl:if test="name() != 'video_dimensions'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if></xsl:if></xsl:if>
	</xsl:for-each>
	<xsl:for-each select="*/*/*">
		<xsl:if test="name() != 'format'"><xsl:if test="name() != 'audio_metrics'">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		</div>
		</xsl:if></xsl:if>
	</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'img'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		 
		</div>
		</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'bib'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		 
		</div>
		</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'gen'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		 
		</div>
		</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'dis'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		 
		</div>
		</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	<xsl:if test="$FIELD = 'dru'">
	<tr style="vertical-align: top;"><td><strong><xsl:value-of select="name()"/></strong></td>
	<td colspan="3">
	<xsl:for-each select="*">
		<div>
		 <xsl:if test="name() != 'nurl'">
		 <xsl:value-of select="name()"/> =  <xsl:value-of select="text()"/>
		 </xsl:if>
		  <xsl:if test="name() = 'nurl'">
		 <xsl:value-of select="name()"/> =  <a href="{text()}"> <xsl:value-of select="text()"/></a>
		 </xsl:if>
		 
		</div>
		</xsl:for-each>
	</td></tr>
	</xsl:if>
	
	</xsl:for-each>


	</tbody></table></div>

</xsl:template>


</xsl:stylesheet>