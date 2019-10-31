<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:param name="selected"/>
	<xsl:template match="/">
		<xsl:apply-templates select="folder"/>
	</xsl:template>
	<xsl:template match="folder">
		<xsl:param name="prefix"></xsl:param>
		<option value="{@id}"><xsl:if test="$selected = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="$prefix"/><xsl:value-of select="@prefix"/>: <xsl:value-of select="@name"/></option>
		<xsl:apply-templates select="folder">
			<xsl:with-param name="prefix"><xsl:value-of select="concat($prefix,'-')"/></xsl:with-param>
		</xsl:apply-templates>
	</xsl:template>
</xsl:stylesheet>

