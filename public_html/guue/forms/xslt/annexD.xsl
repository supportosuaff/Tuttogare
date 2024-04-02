<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : annexD
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 10/01/2018                                                           
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*[@FORM][.//*:D_ACCORDANCE_ARTICLE[not(child::*)]]" mode="R900">
		<xsl:choose>
			<xsl:when test=".[@FORM='F21' or @FORM='F22' or @FORM='F23'][.//*:NOTICE/@*!='AWARD_CONTRACT' or .//*:NOTICE/@*!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'D_ACCORDANCE_ARTICLE empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R900'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]//*:D_ACCORDANCE_ARTICLE//*[@CTYPE!= ancestor::*[@FORM]//*:TYPE_CONTRACT/@*]" mode="R901">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F21' or @FORM='F22' or @FORM='F23'][.//*:NOTICE/@*!='AWARD_CONTRACT' or .//*:NOTICE/@*!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('TYPE_CONTRACT[@CTYPE=',ancestor::*[@FORM]//*:TYPE_CONTRACT/@*,'] and ',local-name(),'[@CTYPE=',@CTYPE,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R901'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][.//*:D_JUSTIFICATION]" mode="R902">
		<xsl:choose>
			<xsl:when test=".[@FORM='F21' or @FORM='F22' or @FORM='F23'][.//*:NOTICE/@*!='AWARD_CONTRACT' or .//*:NOTICE/@*!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:when test=".//*:D_JUSTIFICATION/descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'D_JUSTIFICATION empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R902'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
<!--	<xsl:template match="text()" priority="-1" mode="#all"/>
	<xsl:template match="@*|node()" priority="-2" mode="#all">
		<xsl:apply-templates select="@*|*" mode="#current"/>
	</xsl:template>-->
</xsl:stylesheet>
