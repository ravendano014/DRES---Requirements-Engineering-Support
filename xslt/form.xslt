<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:out="http://www.w3.org/1999/XSL/TransformAlias">
	<xsl:namespace-alias stylesheet-prefix="out" result-prefix="xsl"/>
	<xsl:param name="Display">edit</xsl:param>
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" version="1.0"/>
	<xsl:template match="form">
		<xsl:if test="not(@display) or @display = $Display">
		<out:stylesheet version="1.0">
		<out:output method="html" version="1.0" indent="yes" omit-xml-declaration="yes"/>
		<out:param name="CSSPrefix"></out:param>
		<out:param name="FormTitle"><xsl:value-of select="@title"/></out:param>
		<out:param name="FormAction"><xsl:value-of select="@name"/></out:param>
		<out:param name="FormWidth"><xsl:value-of select="@width"/></out:param>
		<out:param name="ChangeHandler"></out:param>
		<out:param name="SubmitHandler"></out:param>
		<xsl:for-each select="//field[@input]">
			<out:param name="{@input}"/>
		</xsl:for-each>
		<out:template match="*">
		<table>
			<xsl:attribute name="style"><xsl:value-of select="@style"/></xsl:attribute>
			<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormTable</out:attribute>
			<out:attribute name="width"><out:value-of select="$FormWidth"/></out:attribute>
			<out:if test="$FormTitle != ''">
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormTitle</out:attribute>
					<out:value-of select="$FormTitle"/>
				</td>
			</tr>
			</out:if>
			<form method="post">
			<input type="hidden" name="action">
				<out:attribute name="value"><out:value-of select="$FormAction"/></out:attribute>
			</input>
			<xsl:apply-templates select="fieldgroup|field[not(@display) or @display = $Display]"/>
			<xsl:if test="$Display = 'edit' or count(button[@display = 'readonly']) &gt; 0">
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormFooter</out:attribute>
					<xsl:apply-templates select="button[(not(@display) and $Display = 'edit') or @display = $Display]"/>
				</td>
			</tr>
			</xsl:if>
			</form>
		</table>
		</out:template>
		</out:stylesheet>
		</xsl:if>
	</xsl:template>
	<xsl:template match="button">
		<input type="submit" name="{@name}" value="{@label}" onClick="forms_dirty=false">
			<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormButton</out:attribute>
		</input>
	</xsl:template>
	<xsl:template match="//fieldgroup">
			<tr>
				<td colspan="2">
					<table>
						<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGroupTable</out:attribute>
						<tr>
							<td colspan="2">
								<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGroupHeader</out:attribute>
								<xsl:value-of select="@name"/>
							</td>
						</tr>
						<xsl:apply-templates select="field[not(@display) or @display = $Display]"/>
					</table>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//field">
			<tr>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
				<td>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormData</out:attribute>
					<xsl:call-template name="control"/>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//field[@layout='vertical']">
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormLabel</out:attribute>
					<xsl:value-of select="@label"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormData</out:attribute>
					<xsl:call-template name="control"/>
					<xsl:apply-templates/>
				</td>
			</tr>
	</xsl:template>
	<xsl:template match="//field[@control='hidden']">
			<input type="hidden" name="{@name}">
				<out:attribute name="value"><out:value-of select="{@binding}"/></out:attribute>
			</input>
	</xsl:template>
	<xsl:template name="control">
		<xsl:choose>
			<!-- not bound controls -->
			<xsl:when test="@control='grid'">
				<out:choose>
					<out:when test="count({@context}) = 0">
						<i>none</i>
					</out:when>
					<out:otherwise>
						<xsl:call-template name="grid"/>
					</out:otherwise>
				</out:choose>
			</xsl:when>
			<xsl:when test="@control='label' and not(@binding)">
				<xsl:value-of select="@value"/>
			</xsl:when>
			<xsl:when test="(@control='readonly' or $Display='readonly') and not(@binding)">
				<xsl:value-of select="@value"/>
			</xsl:when>
			<xsl:when test="@control='textfield' and not(@binding)">
				<input type="text" name="{@name}" size="{@width}" style="{@style}" value="{@value}">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
				</input>
			</xsl:when>
			<xsl:when test="@control='combo' and not(@binding)">
				<select name="{@name}" style="{@style}">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<xsl:for-each select="choice">
						<option value="{@value}">
							<out:if test="../@value = '{@value}'">
								<out:attribute name="selected">selected</out:attribute>
							</out:if>
							<xsl:value-of select="@name"/>
						</option>
					</xsl:for-each>
				</select>
			</xsl:when>
			<xsl:when test="@control='listbox' and not(@binding)">
				<select name="{@name}" size="{@height}" style="{@style}">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
				</select>
			</xsl:when>
			<xsl:when test="@control='textarea' and not(@binding)">
				<textarea name="{@name}" cols="{@width}" rows="{@height}" style="{@style}">
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<xsl:value-of select="@value"/>
				</textarea>
			</xsl:when>
			<xsl:when test="@control='checkbox' and not(@binding)">
				<input type="checkbox" name="{@name}" value="{choice[@name='on']/@value}" style="{@style}">
					<!--<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>-->
					<out:if test="@value = '{choice[@name='on']/@value}'">
						<out:attribute name="checked">checked</out:attribute>
					</out:if>
				</input>
			</xsl:when>
			<xsl:when test="@control='radio' and not(@binding)">
				<xsl:for-each select="choice">
					<input type="radio" name="{../@name}" value="{@value}" style="{@style}">
						<!--<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>-->
						<out:if test="../@value = '{@value}'">
							<out:attribute name="checked">checked</out:attribute>
						</out:if>
					</input>
					<xsl:value-of select="@name"/><br/>
				</xsl:for-each>
			</xsl:when>
			<!-- XML bound controls -->
			<xsl:when test="@control='label'">
				<out:value-of select="normalize-space({@binding})"/>
			</xsl:when>
			<xsl:when test="@control='listbox' and $Display='readonly'">
				<out:choose>
					<out:when test="count({@context}) = 0">
						<i>none</i>
					</out:when>
					<out:otherwise>
						<out:for-each select="{@context}">
							<out:value-of select="normalize-space({@binding})"/><br/>
						</out:for-each>
					</out:otherwise>
				</out:choose>
			</xsl:when>
			<xsl:when test="@control='readonly' or $Display='readonly'">
				<out:choose>
					<out:when test="not(normalize-space({@binding}))">
						<i>N/A</i>
					</out:when>
					<out:otherwise>
						<out:value-of select="normalize-space({@binding})"/>
					</out:otherwise>
				</out:choose>
			</xsl:when>
			<xsl:when test="@control = 'textfield'">
				<input type="text" name="{@name}" size="{@width}" style="{@style}">
					<xsl:call-template name="onchange"/>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<out:attribute name="value"><!-- <out:value-of select="normalize-space({@binding})"/> --><xsl:call-template name="value"/></out:attribute>
				</input>
			</xsl:when>
			<xsl:when test="@control = 'combo'">
				<select name="{@name}" style="{@style}">
					<xsl:call-template name="onchange"/>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<xsl:for-each select="choice">
						<option value="{@value}">
							<out:if test="{../@binding} = '{@value}'">
								<out:attribute name="selected">selected</out:attribute>
							</out:if>
							<xsl:value-of select="@name"/>
						</option>
					</xsl:for-each>
				</select>
			</xsl:when>
			<xsl:when test="@control='listbox'">
				<select name="{@name}" size="{@height}" style="{@style}">
					<xsl:call-template name="onchange"/>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<out:variable name="DataDoc" select="/"/>
					<out:for-each select="{@context}">
						<option>
							<xsl:if test="@optvalue">
								<out:attribute name="value"><out:value-of select="normalize-space({@optvalue})"/></out:attribute>
								<out:if test="$DataDoc{@binding} = {@optvalue}">
									<out:attribute name="selected">selected</out:attribute>
								</out:if>
							</xsl:if>
							<out:value-of select="normalize-space({@optdisplay})"/>
						</option>
					</out:for-each>
				</select>
			</xsl:when>
			<xsl:when test="@control='textarea'">
				<textarea name="{@name}" cols="{@width}" rows="{@height}" style="{@style}">
					<xsl:call-template name="onchange"/>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>
					<out:value-of select="normalize-space({@binding})"/>
				</textarea>
			</xsl:when>
			<xsl:when test="@control='checkbox'">
				<input type="checkbox" name="{@name}" value="{choice[@name='on']/@value}" style="{@style}">
					<xsl:call-template name="onchange"/>
					<!--<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>-->
					<out:if test="{@binding} = '{choice[@name='on']/@value}'">
						<out:attribute name="checked">checked</out:attribute>
					</out:if>
				</input>
			</xsl:when>
			<xsl:when test="@control='radio'">
				<xsl:for-each select="choice">
					<input type="radio" name="{../@name}" value="{@value}" style="{@style}">
						<xsl:call-template name="onchange"/>
						<!--<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormControl</out:attribute>-->
						<out:if test="{../@binding} = '{@value}'">
							<out:attribute name="checked">checked</out:attribute>
						</out:if>
					</input>
					<xsl:value-of select="@name"/><br/>
				</xsl:for-each>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="grid">
		<table width="{@width}" style="{@style}">
			<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridTable</out:attribute>
			<xsl:choose>
				<xsl:when test="@type='vertical'">
					<out:for-each select="{@context}">
						<xsl:if test="@sort">
							<out:sort select="{@sort}"/>
						</xsl:if>
						<out:variable name="AlternateRow"><out:if test="position() mod 2 = 0">Alt</out:if></out:variable>
						<xsl:for-each select="column">
							<tr>
								<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridRow<out:value-of select="$AlternateRow"/></out:attribute>
								<td>
									<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridLabel</out:attribute>
									<xsl:call-template name="gridheader"/>
								</td>
								<td align="{@align}" width="{@width}" style="{@style}">
				 					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridCell<out:value-of select="$AlternateRow"/></out:attribute>
									<xsl:call-template name="gridcell"/>
								</td>
							</tr>
						</xsl:for-each>
						<xsl:if test="@separator='true'">
						<out:if test="position() != last()">
						<tr>
							<td colspan="2">
								<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridSeparator</out:attribute>
							</td>
						</tr>
						</out:if>
						</xsl:if>
					</out:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<xsl:for-each select="column">
							<td>
								<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridHeader</out:attribute>
								<xsl:call-template name="gridheader"/>
							</td>
						</xsl:for-each>
					</tr>
					<out:for-each select="{@context}">
					<xsl:if test="@sort">
						<out:sort select="{@sort}"/>
					</xsl:if>
					<tr>
						<out:variable name="AlternateRow"><out:if test="position() mod 2 = 0">Alt</out:if></out:variable>
						<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridRow<out:value-of select="$AlternateRow"/></out:attribute>
						<xsl:for-each select="column">
							<td align="{@align}" width="{@width}" style="{@style}">
			 					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormGridCell<out:value-of select="$AlternateRow"/></out:attribute>
								<xsl:call-template name="gridcell"/>
							</td>
						</xsl:for-each>
					</tr>
					</out:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>
	<xsl:template name="gridheader">
		<xsl:value-of select="@name"/>
	</xsl:template>
	<xsl:template name="gridcell">
		<xsl:choose>
			<xsl:when test="link">
				<a>
					<out:attribute name="class"><out:value-of select="$CSSPrefix"/>FormOutLink</out:attribute>
					<out:attribute name="href"><xsl:value-of select="link/@page"/>?<xsl:if test="link/@transfer = 'true'"><out:if test="$TransferParams != ''"><out:value-of select="$TransferParams"/>&amp;</out:if></xsl:if><xsl:for-each select="link/output"><xsl:value-of select="@name"/>=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><out:value-of><xsl:attribute name="select"><xsl:value-of select="@select"/></xsl:attribute></out:value-of></xsl:otherwise></xsl:choose>&amp;</xsl:for-each></out:attribute>
					<out:value-of select="{@binding}"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<out:value-of select="{@binding}"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="onchange">
		<xsl:if test="not(@nochange)">
			<out:if test="$ChangeHandler != ''">
				<out:attribute name="onChange"><out:value-of select="$ChangeHandler"/></out:attribute>
			</out:if>
		</xsl:if>
	</xsl:template>
	<xsl:template name="onsubmit">
		<out:if test="$SubmitHandler != ''">
			<out:attribute name="onSubmit"><out:value-of select="$SubmitHandler"/></out:attribute>
		</out:if>
	</xsl:template>
	<xsl:template name="value">
		<xsl:choose>
			<xsl:when test="@input">
				<out:choose><out:when test="${@input}"><out:value-of select="${@input}"/></out:when><out:otherwise><out:value-of select="normalize-space({@binding})"/></out:otherwise></out:choose>
			</xsl:when>
			<xsl:otherwise>
				<out:value-of select="normalize-space({@binding})"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
