<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section7
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 10/01/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*[@FORM='F14']//*:CHANGE[local-name(*:OLD_VALUE/*[1])!=local-name(*:NEW_VALUE/*[1])]" mode="R800R801">
		<xsl:variable name="ele" select="concat('CHANGE[',position()-1,'][OLD_VALUE/',local-name(*:OLD_VALUE/*[1]) ,' and NEW_VALUE/',local-name(*:NEW_VALUE/*[1]),']')"/>
		<xsl:choose>
			<xsl:when test=".//*:NOTHING and .//*:CPV_MAIN">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R801'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTHING"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R800'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F14']//*:CHANGE[.//*:LOT_NO and .//*:SECTION[matches(text(),'^((III)|(IV)|(VI))')]]" mode="R802">
		<xsl:variable name="ele" select="concat('CHANGE[',position()-1,'][WHERE/LOT_NO=',&quot;'&quot;,.//*:LOT_NO,&quot;'&quot;,' and WHERE/SECTION=',&quot;'&quot;,.//*:SECTION,&quot;'&quot;,']')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R802'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F20']//*:DESCRIPTION_PROCUREMENT/*:SHORT_DESCR" mode="R803">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'DESCRIPTION_PROCUREMENT/SHORT_DESCR empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R803'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F20']//*:INFO_MODIFICATIONS/*:SHORT_DESCR" mode="R804">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'NFO_MODIFICATIONS/SHORT_DESCR empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R804'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F20']//*:INFO_MODIFICATIONS/*:ADDITIONAL_NEED" mode="R805">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'NFO_MODIFICATIONS/ADDITIONAL_NEED empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R805'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F20']//*:INFO_MODIFICATIONS/*:UNFORESEEN_CIRCUMSTANCE" mode="R806">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'NFO_MODIFICATIONS/UNFORESEEN_CIRCUMSTANCE empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R806'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F14']//*:CHANGES[not(.//*:CPV_MAIN) and not(.//*:CPV_ADDITIONAL) and not(.//*:DATE)]" mode="R807">
		<xsl:choose>
			<xsl:when test=".//*:TEXT/descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:when test=".//*:INFO_ADD/descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'INFO_ADD empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R807'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
		<!---->
	<xsl:template match="*[@FORM='F14']//*:CHANGE[.//*:SECTION[matches(text(),'^VII')]]" mode="R808">
		<xsl:variable name="ele" select="concat('CHANGE[',position()-1,'][WHERE/SECTION=',&quot;'&quot;,.//*:SECTION,&quot;'&quot;,']')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R808'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
</xsl:stylesheet>
