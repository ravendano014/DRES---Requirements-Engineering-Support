<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:param name="Title">Requirements report</xsl:param>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" cdata-section-elements="" />
	<xsl:template match="/">
		<html>
		<head>
			<title><xsl:value-of select="$Title"/></title>
			<link rel="stylesheet" href="css/report.css"/>
		</head>
		<body>
		<h1><xsl:value-of select="$Title"/></h1>
		<hr/>
		<xsl:apply-templates/>
		<hr/>
		<p class="Footer">generated with DRES requirements management software | http://dres.sf.net</p>
		</body>
		</html>
	</xsl:template>
	<xsl:template match="folder">
		<h2><xsl:if test="@prefix"><xsl:value-of select="@prefix"/>: </xsl:if><xsl:value-of select="@name"/></h2>
		<div class="Folder">
		<xsl:apply-templates select="requirement"/>
		<xsl:apply-templates select="folder"/>
		</div>
	</xsl:template>
	<xsl:template match="requirement">
		<h3><xsl:if test="@identifier"><xsl:value-of select="@identifier"/>: </xsl:if><xsl:value-of select="@name"/></h3>
		<div class="Requirement">
		<xsl:call-template name="BasicAttributes"/>
		<xsl:apply-templates select="keywords"/>
		<xsl:apply-templates select="revision"/>
		<xsl:apply-templates select="description"/>
		<xsl:apply-templates select="rationale"/>
		<xsl:apply-templates select="source"/>
		<xsl:apply-templates select="viewpoint"/>
		<xsl:apply-templates select="estimates"/>
		<xsl:apply-templates select="definition"/>
		<xsl:apply-templates select="scenarios"/>
		<xsl:apply-templates select="test-cases"/>
		<xsl:apply-templates select="custom-attributes"/>
		</div>
	</xsl:template>
	<xsl:template name="BasicAttributes">
		<table class="AttrTable ReqAttributes">
			<tr>
				<td width="25%" class="AttrLabelCell AttrLabel" nowrap="nowrap">Priority:</td>
				<td width="25%" class="AttrValueCell AttrValue"><xsl:value-of select="@priority"/></td>
				<td width="25%" class="AttrLabelCell AttrLabel" nowrap="nowrap">Status:</td>
				<td width="25%" class="AttrValueCell AttrValue"><xsl:value-of select="@status"/></td>
			</tr>
		</table>
	</xsl:template>
	<xsl:template match="revision">
		<h4 class="ReqRevision">Revision information</h4>
		<table class="AttrTable ReqRevision">
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Date</td>
				<td class="AttrValueCell AttrValue"><xsl:value-of select="@date"/></td>
			</tr>
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Author</td>
				<td class="AttrValueCell AttrValue"><xsl:value-of select="author/text()"/></td>
			</tr>
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Version</td>
				<td class="AttrValueCell AttrValue"><xsl:value-of select="@version"/></td>
			</tr>
			<xsl:if test="@label != ''">
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Label</td>
				<td class="AttrValueCell AttrValue"><xsl:value-of select="@label"/></td>
			</tr>
			</xsl:if>
			<xsl:if test="comment/text()">
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Comment</td>
				<td class="AttrValueCell AttrValue"><xsl:value-of select="comment/text()"/></td>
			</tr>
			</xsl:if>
		</table>
	</xsl:template>	
	<xsl:template match="description">
		<xsl:if test="text()">
			<h4 class="ReqDescription">Description</h4>
			<p class="ReqDescription"><xsl:value-of select="text()"/></p>
		</xsl:if>
	</xsl:template>	
	<xsl:template match="keywords">
		<xsl:if test="count(keyword) &gt; 0">
		<table class="AttrTable ReqKeywords">
			<tr>
				<td class="AttrLabelCell AttrLabel" nowrap="nowrap">Keywords:</td>
				<td class="AttrValueCell AttrValue">
					<xsl:for-each select="keyword">
						<span class="ReqKeyword"><xsl:value-of select="text()"/></span>
						<xsl:if test="position()!=last()">, </xsl:if>				
					</xsl:for-each>
				</td>
			</tr>
		</table>
		</xsl:if>
	</xsl:template>	
	<xsl:template match="rationale">
		<xsl:if test="text()">
			<h4 class="ReqRationale">Rationale</h4>
			<p class="ReqRationale"><xsl:value-of select="text()"/></p>
		</xsl:if>
	</xsl:template>	
	<xsl:template match="source">
		<xsl:if test="text()">
			<h4 class="ReqSource">Source</h4>
			<p class="ReqSource"><xsl:value-of select="text()"/></p>
		</xsl:if>
	</xsl:template>	
	<xsl:template match="viewpoint">
		<xsl:if test="text()">
			<h4 class="ReqViewpoint">Viewpoint</h4>
			<p class="ReqViewpoint"><xsl:value-of select="text()"/></p>
		</xsl:if>
	</xsl:template>
	<xsl:template match="estimates">
		<xsl:if test="count(estimate) + count(@*[string() != '']) &gt; 0">
			<h4 class="ReqEstimates">Estimates</h4>
			<table class="AttrTable ReqEstimates">
				<xsl:for-each select="@*[string() != '']">
				<tr>
					<td class="AttrLabelCell AttrLabel" nowrap="nowrap"><xsl:value-of select="name()"/></td>
					<td class="AttrValueCell AttrValue"><xsl:value-of select="string()"/></td>
				</tr>
				</xsl:for-each>
				<xsl:for-each select="estimate">
				<tr>
					<td class="AttrLabelCell AttrLabel" nowrap="nowrap"><xsl:value-of select="@name"/></td>
					<td class="AttrValueCell AttrValue"><xsl:value-of select="@value"/></td>
				</tr>
				</xsl:for-each>
			</table>
		</xsl:if>
	</xsl:template>
	<xsl:template match="definition">
		<xsl:if test="input/text() != '' or condition/text() != '' or output/text() != '' or processing/text() != '' or count(samples/sample) &gt; 0">
			<h4 class="ReqDefinition">Definition</h4>
			<p class="ReqDefinition">
			<xsl:if test="input/text() != ''">
				<h5 class="ReqDefinitionInput">Input</h5>
				<p class="ReqDefinitionInput"><xsl:value-of select="input/text()"/></p>
			</xsl:if>
			<xsl:if test="condition/text() != ''">
				<h5 class="ReqDefinitionCondition">Condition</h5>
				<p class="ReqDefinitionCondition"><xsl:value-of select="condition/text()"/></p>
			</xsl:if>
			<xsl:if test="output/text() != ''">
				<h5 class="ReqDefinitionOutput">Output</h5>
				<p class="ReqDefinitionOutput"><xsl:value-of select="output/text()"/></p>
			</xsl:if>
			<xsl:if test="processing/text() != ''">
				<h5 class="ReqDefinitionProcessing">Processing</h5>
				<p class="ReqDefinitionProcessing"><xsl:value-of select="processing/text()"/></p>
			</xsl:if>
			<xsl:if test="count(samples/sample) &gt; 0">
				<h4 class="ReqDefinitionSamples">Samples</h4>
				<p class="ReqDefinitionSamples>">
				<xsl:for-each select="samples/sample">
					<h6 class="ReqDefinitionSample"><xsl:value-of select="@name"/></h6>
					<p class="ReqDefinitionSample"><xsl:value-of select="text()"/></p>
				</xsl:for-each>
				</p>
			</xsl:if>
			</p>
		</xsl:if>
	</xsl:template>	
	<xsl:template match="scenarios">
		<xsl:if test="count(scenario) &gt; 0">
			<h4 class="ReqScenarios">Scenarios</h4>
			<p class="ReqScenarios">
			<xsl:for-each select="scenario">
				<h6 class="ReqScenario"><xsl:value-of select="@name"/></h6>
				<p class="ReqScenario"><xsl:value-of select="text()"/></p>
			</xsl:for-each>
			</p>
		</xsl:if>
	</xsl:template>
	<xsl:template match="test-cases">
		<xsl:if test="count(test-case) &gt; 0">
			<h4 class="ReqTestCases">Test cases</h4>
			<p class="ReqTestCases">
			<xsl:for-each select="test-case">
				<h6 class="ReqTestCase"><xsl:value-of select="@name"/></h6>
				<p class="ReqTestCase"><xsl:value-of select="text()"/></p>
			</xsl:for-each>
			</p>
		</xsl:if>
	</xsl:template>
	<xsl:template match="custom-attributes">
		<xsl:if test="count(custom-attribute) &gt; 0">
			<h4 class="ReqCustomAttributes">Custom attributes</h4>
			<table class="AttrTable ReqCustomAttributes">
				<xsl:for-each select="custom-attribute">
				<tr>
					<td class="AttrLabelCell AttrLabel" nowrap="nowrap"><xsl:value-of select="@name"/></td>
					<td class="AttrValueCell AttrValue"><xsl:value-of select="@value"/></td>
				</tr>
				</xsl:for-each>
			</table>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
