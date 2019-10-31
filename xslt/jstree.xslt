<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:param name="MenuName">menu</xsl:param>
	<xsl:template match="/folder">
		var <xsl:value-of select="$MenuName"/> = new MTMenu();
		<xsl:value-of select="$MenuName"/>.MTMAddItem(new MTMenuItem('<b><xsl:value-of select="@prefix"/></b><xsl:text>: </xsl:text><xsl:value-of select="@name"/>','main.php?page=folder&amp;folder=<xsl:value-of select="@id"/>','main'));
		<xsl:if test="count(folder) &gt; 0 ">
			<xsl:call-template name="folder">
				<xsl:with-param name="Parent"><xsl:value-of select="$MenuName"/></xsl:with-param>
				<xsl:with-param name="Position"><xsl:value-of select="0"/></xsl:with-param>
				<xsl:with-param name="VarName">sub<xsl:value-of select="$MenuName"/><xsl:value-of select="position()"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
		MTMStartMenu();
	</xsl:template>
	<xsl:template name="folder">
		<xsl:param name="Parent"/>
		<xsl:param name="Position"/>
		<xsl:param name="VarName"/>
		var <xsl:value-of select="$VarName"/> = new MTMenu();
		<xsl:for-each select="folder">
			<xsl:value-of select="$VarName"/>.MTMAddItem(new MTMenuItem('<b><xsl:value-of select="@prefix"/></b><xsl:text>: </xsl:text><xsl:value-of select="@name"/>','main.php?page=folder&amp;folder=<xsl:value-of select="@id"/>','main'));
			<xsl:if test="count(folder) &gt; 0 ">
				<xsl:call-template name="folder">
					<xsl:with-param name="Parent"><xsl:value-of select="$VarName"/></xsl:with-param>
					<xsl:with-param name="Position"><xsl:value-of select="position()-1"/></xsl:with-param>
					<xsl:with-param name="VarName"><xsl:value-of select="$VarName"/><xsl:value-of select="position()"/></xsl:with-param>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
		<xsl:if test="count(folder) &gt; 0 and $Parent != ''">
			<xsl:value-of select="$Parent"/>.items[<xsl:value-of select="$Position"/>].MTMakeSubmenu(<xsl:value-of select="$VarName"/>);
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>

