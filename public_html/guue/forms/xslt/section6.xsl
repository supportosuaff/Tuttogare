<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section6
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 16/11/2016                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*:EORDERING|*:EINVOICING|*:EPAYMENT" mode="R700R701R702R703R704">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:NOTICE) or ancestor::*[@FORM='F07']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R700'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R701'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R702'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R703'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='CONCESSION_AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R704'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:ADDRESS_REVIEW_BODY|*:ADDRESS_MEDIATION_BODY|*:REVIEW_PROCEDURE|*:ADDRESS_REVIEW_INFO" mode="R705R706">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:NOTICE) or ancestor::*[@FORM='F07']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R705'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R706'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04'][.//*:NOTICE[@*!='PRI_ONLY' and @*!='PER_ONLY'] and not(.//*:ADDRESS_REVIEW_BODY)]" mode="R707">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and missing ADDRESS_REVIEW_BODY')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R707'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F14'][number(replace(.//*:DATE_DISPATCH_ORIGINAL/text(),'-','')) &gt; number(replace(.//*:DATE_DISPATCH_NOTICE/text(),'-',''))]" mode="R708">
		<xsl:variable name="ele" select="concat('DATE_DISPATCH_ORIGINAL=',.//*:DATE_DISPATCH_ORIGINAL/text(),', DATE_DISPATCH_NOTICE=',.//*:DATE_DISPATCH_NOTICE/text())"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R708'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_DISPATCH_NOTICE" mode="R709">
		<xsl:if test="not(matches(normalize-space(text()),'^20'))">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R709'"/>
				<xsl:with-param name="content" select="concat(local-name(.),'=', &quot;'&quot;, normalize-space(./text()), &quot;'&quot;)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
