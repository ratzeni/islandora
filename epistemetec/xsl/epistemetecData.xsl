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
<tr><th colspan="3"><h3>All Data</h3></th></tr>
<xsl:for-each select="/*/*">
	<xsl:variable name="FULLFIELD" select="name()"/>
	<xsl:variable name="FIELD" select="name()"/>
	<xsl:variable name="DATA" select="text()"/>
	<xsl:if test="$DATA != ' '">
	<tr style="vertical-align: top;"><td>
		<xsl:for-each select="*">
		<div>
		<xsl:choose>
		<xsl:when test="name() = 'identifier'">
		 <xsl:value-of select="'Codice'"/> = <xsl:value-of select="text()"/>
		</xsl:when>
		<xsl:when test="name() = 'item'">
		 <xsl:value-of select="'Identificazione'"/> = <xsl:value-of select="text()"/>
		</xsl:when>
		<xsl:when test="name() = 'format'">
		<xsl:value-of select="'Formato'"/> = <xsl:value-of select="'text'"/>
		</xsl:when>
		<xsl:when test="name() = 'subject'">
		 <xsl:value-of select="'Tipologia Risorsa'"/> = <xsl:value-of select="text()"/>
		</xsl:when>
		 <xsl:when test="name() = 'access_rights'">
		 <xsl:value-of select="'Distribuzione'"/> = <xsl:value-of select="text()"/>
		</xsl:when>
        </xsl:choose>
		</div>
		</xsl:for-each>
		
	</td></tr>
	</xsl:if>
	</xsl:for-each>
	<tr><td><xsl:value-of select="'Compatibilità e requisiti'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	<tr><td><xsl:value-of select="'Relazioni Semantiche'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	<tr><td><xsl:value-of select="'TAGS'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	<tr><td><xsl:value-of select="'TAGS definiti dagli utenti'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	<tr><td><xsl:value-of select="'Risorse correlate'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	<tr><td><xsl:value-of select="'Indicazioni suggerite da Utente'"/> = <xsl:value-of select="'NON IMPLEMENTATO'"/></td></tr>
	</tbody></table></div>

</xsl:template>


</xsl:stylesheet>