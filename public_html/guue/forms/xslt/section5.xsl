<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section5
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 08/02/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*[@FORM='F21' or @FORM='F22'][.//*:NOTICE/@*!='AWARD_CONTRACT' and .//*:AWARD_CONTRACT]" mode="R600">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and AWARD_CONTRACT')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R600'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][.//*:NOTICE/@*!='CONCESSION_AWARD_CONTRACT' and .//*:AWARD_CONTRACT]" mode="R601">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and AWARD_CONTRACT')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R601'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F21' or @FORM='F22' or @FORM='F23'][.//*:NOTICE/@*[matches(.,'AWARD')] and not(.//*:AWARD_CONTRACT)]" mode="R608">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and missing AWARD_CONTRACT')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R608'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_CONCLUSION_CONTRACT" mode="R602">
		<xsl:choose>
			<xsl:when test=".//*:NOTICE[@*[matches(.,'CALL|QSU|ONLY|^CONTRACT|PRI')]]"/>
			<xsl:when test="not(number(replace(text(),'-','')) &gt; number(replace(ancestor::*[@FORM]//*:DATE_DISPATCH_NOTICE/text(),'-','')))"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',ancestor::*:AWARD_CONTRACT/@ITEM,']//DATE_CONCLUSION_CONTRACT=',.,',DATE_DISPATCH_NOTICE=',ancestor::*[@FORM]//*:DATE_DISPATCH_NOTICE)"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R602'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F15'][.//*:LEGAL_BASIS/@*!='32009L0081']//AWARDED_SUBCONTRACTING" mode="R604">
		<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=', .//*:LEGAL_BASIS/@*,' and AWARD_CONTRACT[',ancestor::AWARD_CONTRACT/@ITEM,']/AWARDED_SUBCONTRACTING')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R604'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F15'][.//*:LEGAL_BASIS/@*!='32009L0081']//PCT_RANGE_SHARE_SUBCONTRACTING" mode="R605">
		<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=', .//*:LEGAL_BASIS/@*,' and AWARD_CONTRACT[',ancestor::AWARD_CONTRACT/@ITEM,']/PCT_RANGE_SHARE_SUBCONTRACTING')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R605'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F15'][.//*:LEGAL_BASIS/@*='32009L0081']//PCT_RANGE_SHARE_SUBCONTRACTING/MAX[number()>30]" mode="R606">
		<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',ancestor::AWARD_CONTRACT/@ITEM,']/PCT_RANGE_SHARE_SUBCONTRACTING/MAX (value=',number() ,'%)')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R606'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F13'][number(replace(.//*:DATE_DECISION_JURY/text(),'-','')) &gt;= number(replace(.//*:DATE_DISPATCH_NOTICE/text(),'-',''))]" mode="R607">
		<xsl:variable name="ele" select="concat('DATE_DECISION_JURY=',.//*:DATE_DECISION_JURY/text(),', DATE_DISPATCH_NOTICE=',.//*:DATE_DISPATCH_NOTICE/text())"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R607'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM!='F20'][not(.//*:LOT_DIVISION)]//*:AWARD_CONTRACT[.//*:LOT_NO]" mode="R603">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'AWARD'))]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',@ITEM,']/LOT_NO: ')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R603'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM!='F20'][.//*:LOT_DIVISION]//*:AWARD_CONTRACT//*:LOT_NO" mode="R609">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'AWARD'))]"/>
			<xsl:when test="count(ancestor::*[@FORM]//*:OBJECT_CONTRACT)>1"/>
			<xsl:when test="ancestor::*[@FORM]//*:OBJECT_DESCR[not(*:LOT_NO)]"/>
			<xsl:when test="normalize-space() = ancestor::*[@FORM]//*:OBJECT_DESCR/*:LOT_NO/normalize-space()"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',ancestor::*/@ITEM,']/LOT_NO')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R609'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(.//*:FRAMEWORK or .//*:DPS or .//*:PT_INNOVATION_PARTNERSHIP)][.//*:AWARD_CONTRACT]" mode="R610R611">
		<xsl:choose>
			<xsl:when test="@FORM='F20'"/>
			<xsl:when test=".//*:NOTICE/@*[not(matches(.,'AWARD'))]"/>
			<xsl:when test="(@FORM='F06' or @FORM='F22') and .//*:NOTICE_NUMBER_OJ"/>
			<xsl:when test="count(.//*:AWARD_CONTRACT)=1"/>
			<xsl:when test="count(.//*:AWARD_CONTRACT)=count(distinct-values(.//*:AWARD_CONTRACT/*:LOT_NO/normalize-space()))"/>
			<xsl:when test="(@FORM='F06' or @FORM='F22')">
				<xsl:variable name="ele" select="'AWARD_CONTRACT'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R611'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'AWARD_CONTRACT'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R610'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
</xsl:stylesheet>
