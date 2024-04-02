<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section3
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 08/02/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*:LEFTI[child::*]" mode="R400R401R402R403">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R400'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R401'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R402'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='CONCESSION_AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R403'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:RULES_CRITERIA" mode="R404">
		<xsl:choose>
			<xsl:when test="not(descendant::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R404'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:NOTICE[@*!='QSU_ONLY' and @*!='QSU_CALL_COMPETITION']]//*:QUALIFICATION" mode="R405">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*[matches(.,'AWARD|PER')]]"/>
			<xsl:when test="not(descendant::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R405'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F12'][not(.//*:PT_RESTRICTED)]//*:CRITERIA_SELECTION" mode="R406">
		<xsl:choose>
			<xsl:when test="not(descendant::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(ancestor::*[@FORM]//*[matches(local-name(),'^PT_')]),' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R406'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:PARTICULAR_PROFESSION[@CTYPE]" mode="R407">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:OBJECT_CONTRACT[1]//@CTYPE)"/>
			<xsl:when test="ancestor::*[@FORM]//*:OBJECT_CONTRACT[1]//@CTYPE=./@*"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[1]/TYPE_CONTRACT[@CTYPE=', ancestor::*[@FORM]//*:OBJECT_CONTRACT[1]//@CTYPE,'] and ',local-name(.),'[@CTYPE=',@CTYPE,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R407'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F12']//*:PARTICULAR_PROFESSION" mode="R408">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R408'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:COST_PARAMETERS" mode="R409">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R409'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:EXCLUSIVE_RIGHTS_GRANTED" mode="R410">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R410'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:PUBLIC_SERVICE_OBLIGATIONS" mode="R411">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R411'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='T02'][not(.//*:INFORMATION_TICKETS
|.//*:PUNCTUALITY_RELIABILITY
|.//*:CANCELLATIONS_SERVICES
|.//*:CLEANLINESS_ROLLING_STOCK
|.//*:CUST_SATISFACTION_SURVEY
|.//*:COMPLAINT_HANDLING
|.//*:ASSIST_PERSONS_REDUCTED_MOB
|.//*:OTHER_QUALITY_TARGET)]" mode="R412">
		<xsl:variable name="ele" select="'{INFORMATION_TICKETS, PUNCTUALITY_RELIABILITY...} missing'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R412'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:INFORMATION_TICKETS
|*:PUNCTUALITY_RELIABILITY
|*:CANCELLATIONS_SERVICES
|*:CLEANLINESS_ROLLING_STOCK
|*:CUST_SATISFACTION_SURVEY
|*:COMPLAINT_HANDLING
|*:ASSIST_PERSONS_REDUCTED_MOB
|*:OTHER_QUALITY_TARGET" mode="R413">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R413'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:REWARDS_PENALITIES" mode="R414">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R414'"/>
					<xsl:with-param name="content" select="local-name()"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
