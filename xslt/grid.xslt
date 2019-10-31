<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:out="http://www.w3.org/1999/XSL/TransformAlias">
<!-- 
		xmlns:exslt="http://www.exslt.org/functions"
		xmlns:xmlforms="http://ophelia.cs.put.poznan.pl/xmlforms"
		extension-element-prefixes="exslt"
		exclude-result-prefixes="xmlforms">
	<exslt:script language="javascript" implements-prefix="xmlforms">
	<![CDATA[
		function test()
		{
			return "test passed";
		}
	]]>
	</exslt:script>
		
 -->
 	<xsl:param name="CSSPrefix"></xsl:param>
 	<xsl:param name="OrderColumn"><xsl:value-of select="/grid/@sort"/></xsl:param>
	<xsl:param name="OrderDirection"><xsl:if test="not(/grid/@order)">ascending</xsl:if><xsl:value-of select="/grid/@order"/></xsl:param>
	<xsl:param name="Page"></xsl:param>
	<xsl:param name="LocalTransferParams"></xsl:param>
	<xsl:param name="TransferParams"></xsl:param>
	<xsl:param name="InsertTransferParams"></xsl:param>
	<xsl:param name="CurrentPage">1</xsl:param>
	<xsl:param name="RowID"><xsl:if test="not(/grid/@rowid)">*</xsl:if><xsl:value-of select="/grid/@rowid"/></xsl:param>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" cdata-section-elements=""/>
	<xsl:namespace-alias stylesheet-prefix="out" result-prefix="xsl"/>
	<xsl:template match="grid">
		<out:stylesheet version="1.0">
		<out:param name="GridName"><xsl:value-of select="@name"/></out:param>
		<out:param name="GridTitle"><xsl:value-of select="@title"/></out:param>
		<out:param name="RowsPerPage"><xsl:value-of select="pager/@rowsperpage"/></out:param>
		<out:param name="CurrentPage">1</out:param>
		<out:param name="SelectedID"></out:param>
		<out:template match="/">
		<table>
			<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridTable</xsl:attribute>
			<xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
			<tr>
				<td>
					<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridTitle</xsl:attribute>
					<xsl:attribute name="colspan"><xsl:value-of select="count(column)"/></xsl:attribute>
					<out:value-of select="$GridTitle"/>
				</td>
			</tr>
			<tr>
				<xsl:for-each select="column">
					<td>
						<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridHeader</xsl:attribute>
						<xsl:call-template name="sorter"/>
					</td>
				</xsl:for-each>
			</tr>
			<out:variable name="FirstRow" select="($CurrentPage - 1) * $RowsPerPage + 1"/>
			<out:variable name="LastRow" select="$CurrentPage * $RowsPerPage"/>
			<xsl:if test="norows">
				<out:if test="count({@rowbinding}) = 0">
				<tr>
					<td>
						<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridNoRows</xsl:attribute>
						<xsl:attribute name="colspan"><xsl:value-of select="count(column)"/></xsl:attribute>
						<xsl:value-of select="norows"/>
					</td>
				</tr>
				</out:if>
			</xsl:if>
			<out:for-each select="{@rowbinding}">
				<out:sort>
 					<xsl:attribute name="select"><xsl:if test="$OrderColumn = ''">*</xsl:if><xsl:value-of select="column[@name=$OrderColumn]/@binding"/></xsl:attribute>
					<xsl:attribute name="order"><xsl:value-of select="$OrderDirection"/></xsl:attribute>
					<xsl:attribute name="data-type"><xsl:if test="not(column[@name=$OrderColumn]/@type)">text</xsl:if><xsl:value-of select="column[@name=$OrderColumn]/@type"/></xsl:attribute>
				</out:sort>
				<out:if test="position() &gt;= $FirstRow and position() &lt;= $LastRow">
				<tr>
					<out:variable name="SelectedRow">
						<out:choose>
							<out:when test="$SelectedID and {$RowID} = $SelectedID">Selected</out:when>
							<out:otherwise></out:otherwise>
						</out:choose>
					</out:variable>
					<out:variable name="AlternateRow"><out:if test="position() mod 2 = 0 and $SelectedRow = ''">Alt</out:if></out:variable>
 					<out:attribute name="onMouseOver">this.className='<xsl:value-of select="$CSSPrefix"/>HighlightedRow';</out:attribute>
 					<out:attribute name="onMouseOut">this.className='<xsl:value-of select="$CSSPrefix"/>GridRow<out:value-of select="$SelectedRow"/><out:value-of select="$AlternateRow"/>';</out:attribute>
 					<out:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridRow<out:value-of select="$SelectedRow"/><out:value-of select="$AlternateRow"/></out:attribute>
					<xsl:for-each select="column">
						<td>
		 					<out:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridCell<out:value-of select="$SelectedRow"/><out:value-of select="$AlternateRow"/></out:attribute>
		 					<out:attribute name="align"><xsl:value-of select="@align"/></out:attribute>
		 					<out:attribute name="width"><xsl:value-of select="@width"/></out:attribute>
							<xsl:call-template name="cell"/>
							
						</td>
					</xsl:for-each>
				</tr>
				</out:if>
			</out:for-each>
			<tr>
				<td>
					<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>GridFooter</xsl:attribute>
					<xsl:attribute name="colspan"><xsl:value-of select="count(column)"/></xsl:attribute>
					<xsl:call-template name="insert"/>
					<xsl:call-template name="pager"/>
				</td>
			</tr>
		</table>
		</out:template>
		</out:stylesheet>
	</xsl:template>
	<xsl:template name="sorter">
		<xsl:choose>
			<xsl:when test="@sorter = 'simple'">
				<a>
					<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>SorterLink</xsl:attribute>
					<out:attribute name="href"><xsl:value-of select="$Page"/>?<xsl:if test="$LocalTransferParams != ''"><xsl:value-of select="$LocalTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != ''"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><out:value-of select="$GridName"/>_sort=<xsl:value-of select="@name"/>&amp;<xsl:if test="$OrderColumn = @name"><xsl:value-of select="../@name"/>_order=<xsl:choose><xsl:when test="$OrderDirection = 'ascending'">descending</xsl:when><xsl:when test="$OrderDirection = 'descending'">ascending</xsl:when></xsl:choose>&amp;</xsl:if><out:value-of select="$GridName"/>_page=<xsl:value-of select="$CurrentPage"/></out:attribute>
					<xsl:value-of select="@title"/>
				</a>
				<out:text><xsl:text> </xsl:text></out:text>
				<xsl:if test="$OrderColumn = @name and $OrderDirection = 'ascending'"><img border="0" src="img/AscOn.gif"/></xsl:if>
				<xsl:if test="$OrderColumn = @name and $OrderDirection = 'descending'"><img border="0" src="img/DescOn.gif"/></xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@title"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="pager">
		<out:variable name="TotalPages" select="ceiling(count({@rowbinding}) div $RowsPerPage)"/>
		<out:if test="$TotalPages &gt; 1">
			<xsl:choose>
				<xsl:when test="pager/@style = 'simple'">
					<a><xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>NavLink</xsl:attribute><out:attribute name="href"><xsl:value-of select="$Page"/>?<xsl:if test="$LocalTransferParams != ''"><xsl:value-of select="$LocalTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != ''"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:if test="$OrderColumn != ''"><xsl:value-of select="@name"/>_sort=<xsl:value-of select="$OrderColumn"/>&amp;</xsl:if><xsl:if test="$OrderColumn != '' and $OrderDirection != ''"><xsl:value-of select="@name"/>_order=<xsl:value-of select="$OrderDirection"/>&amp;</xsl:if><out:value-of select="$GridName"/>_page=1</out:attribute><img border="0" src="img/FirstOn.gif"/></a>
					<out:text><xsl:text> </xsl:text></out:text>
					<a><xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>NavLink</xsl:attribute><out:attribute name="href"><xsl:value-of select="$Page"/>?<xsl:if test="$LocalTransferParams != ''"><xsl:value-of select="$LocalTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != ''"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:if test="$OrderColumn != ''"><xsl:value-of select="@name"/>_sort=<xsl:value-of select="$OrderColumn"/>&amp;</xsl:if><xsl:if test="$OrderColumn != '' and $OrderDirection != ''"><xsl:value-of select="@name"/>_order=<xsl:value-of select="$OrderDirection"/>&amp;</xsl:if><out:value-of select="$GridName"/>_page=<xsl:if test="$CurrentPage - 1 &lt; 1">1</xsl:if><xsl:if test="$CurrentPage - 1 &gt;= 1"><xsl:value-of select="$CurrentPage - 1"/></xsl:if></out:attribute><img border="0" src="img/PrevOn.gif"/></a>
					<out:text><xsl:text> </xsl:text></out:text>
					Page <out:value-of select="$CurrentPage"/> of <out:value-of select="$TotalPages"/>
					<out:text><xsl:text> </xsl:text></out:text>
					<a><xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>NavLink</xsl:attribute><out:attribute name="href"><xsl:value-of select="$Page"/>?<xsl:if test="$LocalTransferParams != ''"><xsl:value-of select="$LocalTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != ''"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:if test="$OrderColumn != ''"><xsl:value-of select="@name"/>_sort=<xsl:value-of select="$OrderColumn"/>&amp;</xsl:if><xsl:if test="$OrderColumn != '' and $OrderDirection != ''"><xsl:value-of select="@name"/>_order=<xsl:value-of select="$OrderDirection"/>&amp;</xsl:if><out:value-of select="$GridName"/>_page=<out:if test="$CurrentPage + 1 &gt; $TotalPages"><out:value-of select="$TotalPages"/></out:if><out:if test="$CurrentPage + 1 &lt;= $TotalPages"><out:value-of select="$CurrentPage + 1"/></out:if></out:attribute><img border="0" src="img/NextOn.gif"/></a>
					<out:text><xsl:text> </xsl:text></out:text>
					<a><xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>NavLink</xsl:attribute><out:attribute name="href"><xsl:value-of select="$Page"/>?<xsl:if test="$LocalTransferParams != ''"><xsl:value-of select="$LocalTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != ''"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:if test="$OrderColumn != ''"><xsl:value-of select="@name"/>_sort=<xsl:value-of select="$OrderColumn"/>&amp;</xsl:if><xsl:if test="$OrderColumn != '' and $OrderDirection != ''"><xsl:value-of select="@name"/>_order=<xsl:value-of select="$OrderDirection"/>&amp;</xsl:if><out:value-of select="$GridName"/>_page=<out:value-of select="$TotalPages"/></out:attribute><img border="0" src="img/LastOn.gif"/></a>
				</xsl:when>
			</xsl:choose>
		</out:if>
	</xsl:template>
	<xsl:template name="insert">
		<xsl:if test="insert">
			<a>
				<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>InsertLink</xsl:attribute>
				<out:attribute name="href"><xsl:value-of select="insert/@page"/>?<xsl:if test="$InsertTransferParams != ''"><xsl:value-of select="$InsertTransferParams"/>&amp;</xsl:if><xsl:if test="$TransferParams != '' and insert/@transfer = 'true'"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:for-each select="insert/output"><xsl:value-of select="@name"/>=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><out:value-of><xsl:attribute name="select"><xsl:value-of select="@select"/></xsl:attribute></out:value-of></xsl:otherwise></xsl:choose>&amp;</xsl:for-each></out:attribute>
				<xsl:value-of select="insert/@title"/>
			</a>
		</xsl:if>
	</xsl:template>
	<xsl:template name="cell">
		<xsl:choose>
			<xsl:when test="link">
				<a>
					<xsl:attribute name="class"><xsl:value-of select="$CSSPrefix"/>OutLink</xsl:attribute>
					<xsl:if test="link/@target">
						<xsl:attribute name="target"><xsl:value-of select="link/@target"/></xsl:attribute>
					</xsl:if>
					<out:attribute name="href"><xsl:value-of select="link/@page"/>?<xsl:if test="$TransferParams != '' and link/@transfer = 'true'"><xsl:value-of select="$TransferParams"/>&amp;</xsl:if><xsl:for-each select="link/output"><xsl:value-of select="@name"/>=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><out:value-of><xsl:attribute name="select"><xsl:value-of select="@select"/></xsl:attribute></out:value-of></xsl:otherwise></xsl:choose>&amp;</xsl:for-each></out:attribute>
					<out:value-of>
						<xsl:attribute name="select"><xsl:value-of select="@binding"/></xsl:attribute>
					</out:value-of>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<out:value-of>
					<xsl:attribute name="select"><xsl:value-of select="@binding"/></xsl:attribute>
				</out:value-of>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
