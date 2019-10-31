<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:param name="name"></xsl:param>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:template match="/">
		<div style="background-color:white;border:2px solid black">
			<xsl:if test="$name != ''"><b><font size="+1"><xsl:value-of select="$name"/></font></b></xsl:if>
			<xsl:apply-templates select="*"/>
		</div>
	</xsl:template>
	<xsl:template match="*">
		<div style="margin-left:20px;border-left:1px dotted #DDDDDD">
		<b>&lt;<xsl:value-of select="name()"/></b>
			<xsl:apply-templates select="attribute::*"/>
			<xsl:choose>
				<xsl:when test="not(text()) and count(*) = 0">
					<b>/&gt;</b><br/>
				</xsl:when>
				<xsl:otherwise>
					<b>&gt;</b><br/>
					<xsl:if test="text()"><div style="margin-left:10px"><i><font color="gray"><xsl:value-of select="text()"/></font></i></div></xsl:if>
					<xsl:apply-templates select="*"/>
					<b>&lt;/<xsl:value-of select="name()"/>&gt;</b>
				</xsl:otherwise>			
			</xsl:choose>
		</div>
	</xsl:template>
	<xsl:template match="attribute::*">
		<div style="margin-left:10px"><font color="green"><xsl:value-of select="name()"/></font>=<xsl:value-of select="."/></div>
	</xsl:template>
</xsl:stylesheet>

