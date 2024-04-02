<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : length
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                
#  Last update : 16/05/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!--limited to 200-->
	<xsl:template match="*:PROCUREMENT_LAW
|*:TITLE
|*:MAIN_SITE" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>200)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'200'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--limited to 400-->
	<xsl:template match="*:CALCULATION_METHOD
|*:ESTIMATED_TIMING
|*:EU_PROGR_RELATED
|*:INFO_ADD_EAUCTION
|*:INFO_ADD_SUBCONTRACTING
|*:INFO_ADD_VALUE
|*:JUSTIFICATION
|*:LOT_COMBINING_CONTRACT_RIGHT
|*:OBJECT_CONTRACT//*:INFO_ADD
|*:PARTICULAR_PROFESSION
|*:PLACE
|*:PROCEDURE//*:INFO_ADD
|*:RENEWAL_DESCR" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>400)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'400'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--limited to 500-->
	<xsl:template match="*:MAIN_FEATURES_AWARD" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>500)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'500'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--limited to 1000-->
	<xsl:template match="
*:ACCELERATED_PROC
|*:ADDITIONAL_NEED
|*:CONDITIONS
|*:DEPOSIT_GUARANTEE_REQUIRED
|*:LEGAL_FORM
|*:MAIN_FINANCING_CONDITION
|*:METHODS
|*:PERFORMANCE_CONDITIONS
|*:RULES_CRITERIA
|*:OBJECT_CONTRACT/*:SHORT_DESCR
|*:UNFORESEEN_CIRCUMSTANCE" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>1000)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'1000'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--limited to 1500-->
	<xsl:template match="
*:CRITERIA_EVALUATION
|*:DETAILS_PAYMENT
|*:NUMBER_VALUE_PRIZE
|*:REFERENCE_TO_LAW" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>1500)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'1500'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!--limited to 4000-->
	<xsl:template match="
*:ASSIST_PERSONS_REDUCTED_MOB
|*:CANCELLATIONS_SERVICES
|*:CLEANLINESS_ROLLING_STOCK
|*:COMPLAINT_HANDLING
|*:COST_PARAMETERS
|*:CRITERIA_CANDIDATE
|*:CRITERIA_SELECTION
|*:CUST_SATISFACTION_SURVEY
|*:ECONOMIC_FINANCIAL_INFO
|*:ECONOMIC_FINANCIAL_MIN_LEVEL
|*:EXCLUSIVE_RIGHTS_GRANTED
|*:D_JUSTIFICATION
|*:LIST
|*:SIGNIFICANCE
|*:PREDOMINANCE
|*:COMPLEMENTARY_INFO/*:INFO_ADD
|*:CHANGES/*:INFO_ADD
|*:PUBLIC_SERVICE_OBLIGATIONS
|*:OPTIONS_DESCR
|*:OTHER_PARTICULAR_CONDITIONS
|*:OTHER_QUALITY_TARGET
|*:PUNCTUALITY_RELIABILITY
|*:REVIEW_PROCEDURE
|*:REWARDS_PENALITIES
|*:OBJECT_DESCR/*:SHORT_DESCR
|*:MODIFICATIONS_CONTRACT//*:SHORT_DESCR
|*:SOCIAL_STANDARDS
|*:SUITABILITY
|*:TECHNICAL_PROFESSIONAL_INFO
|*:TECHNICAL_PROFESSIONAL_MIN_LEVEL
|*:TEXT
|*:INFORMATION_TICKETS" mode="R999">
		<xsl:choose>
			<xsl:when test="not(string-length()>4000)"/>
			<xsl:otherwise>
				<xsl:call-template name="length">
					<xsl:with-param name="path" select="."/>
					<xsl:with-param name="size" select="'4000'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template name="length">
		<xsl:param name="path"/>
		<xsl:param name="size"/>
		<xsl:choose>
			<xsl:when test="$path/ancestor::*:OBJECT_DESCR">
				<xsl:variable name="ele" select="concat('string-length(OBJECT_CONTRACT[',$path/ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',$path/ancestor::*:OBJECT_DESCR/@ITEM,']//',$path/local-name(),') exceeds ',$size)"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R999'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$path/ancestor::*:OBJECT_CONTRACT">
				<xsl:variable name="ele" select="concat('string-length(OBJECT_CONTRACT[',$path/ancestor::*:OBJECT_CONTRACT/@ITEM,']//',$path/local-name(),') exceeds ',$size)"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R999'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$path/ancestor::*:AWARD_CONTRACT">
				<xsl:variable name="ele" select="concat('string-length(AWARD_CONTRACT[',$path/ancestor::*:AWARD_CONTRACT/@ITEM,']//',$path/local-name(),') exceeds ',$size)"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R999'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<!--choice 1: to display the element name-->
				<xsl:variable name="ele" select="concat('string-length(',$path/local-name(),') exceeds ',$size)"/>
				<!--choice 2: to display the full xpath of the element-->
				<!--<xsl:for-each select="$path/ancestor-or-self::*">
					<xsl:value-of select="concat('/',local-name())"/>
					<xsl:if test="(preceding-sibling::*|following-sibling::*)[local-name()=local-name(current())]">
						<xsl:value-of select="concat('[',count(preceding-sibling::*[local-name()=local-name(current())])+1,']')"/>
					</xsl:if>
				</xsl:for-each>-->
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R999'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
