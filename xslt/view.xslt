<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:out="http://www.w3.org/1999/XSL/TransformAlias">
	<xsl:namespace-alias stylesheet-prefix="out" result-prefix="xsl"/>
	<xsl:param name="TransferParams"></xsl:param>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" cdata-section-elements="" />
	<xsl:template match="view">
		<out:stylesheet version="1.0">
		<out:param name="CSSPrefix"></out:param>
		<out:param name="ViewTitle"><xsl:value-of select="@title"/></out:param>
		<out:param name="ViewFooter"><xsl:value-of select="footer"/></out:param>
		<out:template match="*">
		<table>
			<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewTable</out:attribute>
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewTitle</out:attribute>
					<out:value-of select="$ViewTitle"/>
				</td>
			</tr>
			<xsl:apply-templates select="item"/>
			<out:if test="$ViewFooter != ''">
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewFooter</out:attribute>
					<out:value-of select="$ViewFooter"/>
				</td>
			</tr>
			</out:if>
		</table>
		</out:template>
		</out:stylesheet>
	</xsl:template>
	<xsl:template match="//item[@type='label']">
			<tr>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewData</out:attribute>
					<div>
					<xsl:if test="@class"><xsl:value-of select="@class"/></xsl:if>
					<out:value-of>
						<xsl:attribute name="select"><xsl:value-of select="@binding"/></xsl:attribute>
					</out:value-of>
					</div>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//item[@type='image']">
			<tr>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewData</out:attribute>
					<div>
					<xsl:if test="@class"><xsl:value-of select="@class"/></xsl:if>
					<img>
						<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewData</out:attribute>
						<out:attribute name="src">
							<out:value-of>
								<xsl:attribute name="select"><xsl:value-of select="@binding"/></xsl:attribute>
							</out:value-of>
						</out:attribute>
					</img>
					</div>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//item[@type='link']">
			<tr>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewData</out:attribute>
					<div>
					<xsl:if test="@class"><xsl:value-of select="@class"/></xsl:if>
					<a>
						<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewLink</out:attribute>
						<out:attribute name="href"><xsl:value-of select="link/@page"/>?<xsl:if test="$TransferParams != '' and link/@transfer = 'true'"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:for-each select="link/output"><xsl:value-of select="@name"/>=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><out:value-of><xsl:attribute name="select"><xsl:value-of select="@select"/></xsl:attribute></out:value-of></xsl:otherwise></xsl:choose>&amp;</xsl:for-each></out:attribute>
						<out:value-of>
							<xsl:attribute name="select"><xsl:value-of select="@binding"/></xsl:attribute>
						</out:value-of>
					</a>
					</div>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//item[@type='handler']">
			<tr>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>ViewData</out:attribute>
					<div>
					<xsl:if test="@class"><xsl:value-of select="@class"/></xsl:if>
						<out:processing-instruction name="php">echo <xsl:value-of select="@function"/>("<out:value-of select="{@binding}"/>")</out:processing-instruction>
					</div>
				</td>
			</tr>
	</xsl:template>
</xsl:stylesheet>
