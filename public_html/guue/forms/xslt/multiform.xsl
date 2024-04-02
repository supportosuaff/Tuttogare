<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : multiforms
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                
#  Last update : 08/06/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!-- 
####################################################################################
MOVE                                                           
####################################################################################
 -->
	<xsl:template mode="MOVE" match="
	*[@FORM='T01']//*:ESSENTIAL_ASSETS|
	*[@FORM='T01']//*:LEFTI|
	*[@FORM='T01']//*:AWARD_CONTRACT|
	*[@FORM='T02']//*:PROCEDURE">
		<xsl:call-template name="forms">
			<xsl:with-param name="path" select="."/>
		</xsl:call-template>
	</xsl:template>
	<!-- 
####################################################################################
DEVCO                                                           
####################################################################################
 -->
	<xsl:template mode="DEVCO00" match="
	*[@FORM!='D01']//*:NOTICE|
	*[@FORM='D20']//*:CONTRACTING_BODY/*[local-name()!='ADDRESS_CONTRACTING_BODY']|	
	*[@FORM!='D01' and @FORM!='D02']//*:DOCUMENT_FULL|
	*[@FORM!='D01' and @FORM!='D02']//*:DOCUMENT_RESTRICTED|
	*[@FORM!='D01' and @FORM!='D02']//*:ADDRESS_FURTHER_INFO_IDEM|
	*[@FORM!='D01' and @FORM!='D02']//*:ADDRESS_FURTHER_INFO|
	*[@FORM!='D01' and @FORM!='D02']//*:URL_PARTICIPATION|
	*[@FORM!='D01' and @FORM!='D02']//*:ADDRESS_PARTICIPATION_IDEM|
	*[@FORM!='D01' and @FORM!='D02']//*:ADDRESS_PARTICIPATION|
	*[@FORM!='D01' and @FORM!='D02']//*:URL_TOOL|	
	*[@FORM!='D01']//*:OBJECT_CONTRACT[position()>1]|	
	*[@FORM='D20']//*:OBJECT_CONTRACT/*:SHORT_DESCR|
	*[@FORM!='D01' and @FORM!='D02']//*:OBJECT_CONTRACT/*:VAL_ESTIMATED_TOTAL|
	*[@FORM!='D03']//*:OBJECT_CONTRACT/*[matches(local-name(),'^VAL_(T|R)')]|
	*[@FORM='D03']//*:LOT_DIVISION/*|
	*[@FORM='D20']//*[matches(local-name(),'LOT_DIVISION')]|
	*[@FORM!='D01']//*:DATE_PUBLICATION_NOTICE|
	*[@FORM='D20']//*:OBJECT_DESCR[position()>1]|
	*[@FORM='D20']//*:AC|
	*[@FORM='D03']//*:AC_PROCUREMENT_DOC|
	*[@FORM='D03' or @FORM='D20']//*:VAL_OBJECT|
	*[@FORM='D03']//*:OBJECT_DESCR/*:DURATION|
	*[@FORM='D03']//*:OBJECT_DESCR/*:DATE_START|
	*[@FORM='D03']//*:OBJECT_DESCR/*:DATE_END|
	*[@FORM!='D20']//*:OBJECT_DESCR/*:JUSTIFICATION|
	*[@FORM='D01']//*:NO_RENEWAL|
	*[@FORM='D01']//*:NO_ACCEPTED_VARIANTS|
	*[@FORM='D03' or @FORM='D20']//*[matches(local-name(),'RENEWAL|ACCEPTED_VARIANTS')]|
	*[@FORM!='D02']//*[matches(local-name(),'CANDIDATE')]|
	*[@FORM='D01']//*:NO_OPTIONS|
	*[@FORM='D20']//*[matches(local-name(),'OPTIONS')]|
	*[@FORM!='D02']//*:ECATALOGUE_REQUIRED|
	*[@FORM='D20']//*:OBJECT_DESCR/*:INFO_ADD|
	*[@FORM='D03' or @FORM='D20']//*:LEFTI|	
	*[@FORM='D20']//*:PROCEDURE/*[local-name()!='NOTICE_NUMBER_OJ']|
	*[@FORM='D01']//*:PT_OPEN|
	*[@FORM='D01']//*:PT_COMPETITIVE_DIALOGUE|
	*[@FORM='D01']//*:PT_INNOVATION_PARTNERSHIP|
	*[@FORM!='D03']//*:PT_AWARD_CONTRACT_WITHOUT_CALL|
	*[@FORM='D03']//*:FRAMEWORK/*|
	*[@FORM='D03']//*:DPS_ADDITIONAL_PURCHASERS|	
    *[@FORM!='D02']//*:REDUCTION_RECOURSE|	
	*[@FORM!='D02']//*:RIGHT_CONTRACT_INITIAL_TENDERS|
	*[@FORM='D03']//*:INFO_ADD_EAUCTION|
	*[@FORM='D01']//*:NOTICE_NUMBER_OJ|
	*[@FORM='D03']//*:DATE_RECEIPT_TENDERS|
	*[@FORM!='D02']//*:DATE_DISPATCH_INVITATIONS|
	*[@FORM!='D03']//*:TERMINATION_DPS|
	*[@FORM!='D03']//*:TERMINATION_PIN|
	*[@FORM='D03']//*:LANGUAGES|
	*[@FORM!='D01']//*:DATE_AWARD_SCHEDULED|
	*[@FORM!='D02']//*:DATE_TENDER_VALID|
	*[@FORM!='D02']//*:DURATION_TENDER_VALID|
	*[@FORM!='D02']//*:OPENING_CONDITION|	
	*[@FORM='D01' or @FORM='D02']//*:AWARD_CONTRACT|
	*[@FORM='D20']//*:AWARD_CONTRACT[position()>1]|
	*[@FORM='D20']//*:NO_AWARDED_CONTRACT|
	*[@FORM='D20']//*:TENDERS|
	*[@FORM='D20']//*:VALUES/*:VAL_ESTIMATED_TOTAL|
	*[@FORM='D20']//*:VALUES/*:VAL_RANGE_TOTAL|
	*[@FORM='D20']//*:LIKELY_SUBCONTRACTED|
	*[@FORM='D20']//*:VAL_SUBCONTRACTING|
	*[@FORM='D20']//*:PCT_SUBCONTRACTING|
	*[@FORM='D20']//*:INFO_ADD_SUBCONTRACTING|	
	*[@FORM!='D02']//*:RECURRENT_PROCUREMENT|
	*[@FORM!='D02']//*:NO_RECURRENT_PROCUREMENT|
	*[@FORM='D03' or @FORM='D20']//*:EORDERING|
	*[@FORM='D03' or @FORM='D20']//*:EINVOICING|
	*[@FORM='D03' or @FORM='D20']//*:EPAYMENT|
	*[@FORM!='D20']//*:MODIFICATIONS_CONTRACT
	">
		<xsl:call-template name="forms">
			<xsl:with-param name="path" select="."/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO01" match="*[@FORM='D01'][not(*:NOTICE)]">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R996'"/>
			<xsl:with-param name="content" select="'D01 form and missing NOTICE'"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO01" match="*[@FORM='D01']/*:CONTRACTING_BODY">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'ADDRESS_FURTHER_INFO, CA_TYPE, CA_ACTIVITY'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D01 form and missing CONTRACTING_BODY/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO01" match="*[@FORM='D01']//*:OBJECT_CONTRACT">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'SHORT_DESCR, LOT_DIVISION'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node/*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D01 form and missing OBJECT_CONTRACT[',$node/@ITEM,']/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO01" match="*[@FORM='D01']/*:PROCEDURE">
		<xsl:variable name="ele" select="'CONTRACT_COVERED_GPA'"/>
		<xsl:if test="not (.//*[matches(local-name(),$ele)])">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R996'"/>
				<xsl:with-param name="content" select="concat('D01 form and missing PROCEDURE/',$ele)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO02" match="*[@FORM='D02']/*:CONTRACTING_BODY">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'DOCUMENT_FULL|DOCUMENT_RESTRICTED,ADDRESS_FURTHER_INFO,URL_PARTICIPATION|ADDRESS_PARTICIPATION,CA_TYPE,CA_ACTIVITY'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D02 form and missing CONTRACTING_BODY/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO02" match="*[@FORM='D02']//*:OBJECT_CONTRACT[1]">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'SHORT_DESCR, LOT_DIVISION'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node/*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D02 form and missing OBJECT_CONTRACT[1]/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
		<xsl:if test="*:LOT_DIVISION[not(*:LOT_ALL|*:LOT_ONE_ONLY|*:LOT_MAX_NUMBER)]">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R996'"/>
				<xsl:with-param name="content" select="'D02 form and missing (LOT_ALL,LOT_ONE_ONLY,LOT_MAX_NUMBER)'"/>
			</xsl:call-template>
		</xsl:if>
		<xsl:apply-templates mode="DEVCO02" select="*:OBJECT_DESCR"/>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO02" match="*[@FORM='D02']//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'AC_COST|AC_PRICE|AC_PROCUREMENT_DOC,DURATION|DATE_START|DATE_END,RENEWAL,ACCEPTED_VARIANTS,OPTIONS,EU_PROGR_RELATED'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D02 form and missing OBJECT_DESCR[',$node/@ITEM,']//',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO02" match="*[@FORM='D02']/*:PROCEDURE">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'PT_OPEN|PT_RESTRICTED|PT_COMPETITIVE_NEGOTIATION|PT_COMPETITIVE_DIALOGUE|PT_INNOVATION_PARTNERSHIP,CONTRACT_COVERED_GPA,DATE_RECEIPT_TENDERS,LANGUAGES'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D02 form and missing PROCEDURE/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO02" match="*[@FORM='D02']/*:COMPLEMENTARY_INFO">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'RECURRENT_PROCUREMENT,ADDRESS_REVIEW_BODY'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D02 form and missing COMPLEMENTARY_INFO//',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']/*:CONTRACTING_BODY">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'CA_TYPE, CA_ACTIVITY'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D03 form and missing CONTRACTING_BODY/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']//*:OBJECT_CONTRACT[1]">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'SHORT_DESCR, LOT_DIVISION'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node/*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D03 form and missing OBJECT_CONTRACT[1]/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
		<xsl:apply-templates mode="DEVCO03" select="*:OBJECT_DESCR"/>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'AC_COST|AC_PRICE|AC_PROCUREMENT_DOC,OPTIONS,EU_PROGR_RELATED'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D03 form and missing OBJECT_DESCR[',$node/@ITEM,']//',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']/*:PROCEDURE">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'PT_OPEN|PT_RESTRICTED|PT_COMPETITIVE_NEGOTIATION|PT_COMPETITIVE_DIALOGUE|PT_INNOVATION_PARTNERSHIP|PT_AWARD_CONTRACT_WITHOUT_CALL,CONTRACT_COVERED_GPA'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not ($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D03 form and missing PROCEDURE/',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03'][not(*:AWARD_CONTRACT)]">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R996'"/>
			<xsl:with-param name="content" select="'D03 form and missing AWARD_CONTRACT'"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']//*:AWARDED_CONTRACT[not(.//*:TENDERS)]	">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R996'"/>
			<xsl:with-param name="content" select="'D03 form and missing AWARD_CONTRACT[',ancestor::*/@ITEM,']/AWARDED_CONTRACT/TENDERS'"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO03" match="*[@FORM='D03']/*:COMPLEMENTARY_INFO">
		<xsl:variable name="ele" select="'ADDRESS_REVIEW_BODY'"/>
		<xsl:if test="not (.//*[matches(local-name(),$ele)])">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R996'"/>
				<xsl:with-param name="content" select="concat('D03 form and missing COMPLEMENTARY_INFO/',$ele)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO20" match="*[@FORM='D20']//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR[1]">
		<xsl:variable name="node" select="."/>
		<xsl:variable name="eles" select="'DURATION|DATE_START|DATE_END,EU_PROGR_RELATED'"/>
		<xsl:for-each select="tokenize($eles, ',')">
			<xsl:variable name="ele" select="normalize-space(.)"/>
			<xsl:if test="not($node//*[matches(local-name(),$ele)])">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R996'"/>
					<xsl:with-param name="content" select="concat('D20 form and missing OBJECT_DESCR[',$node/@ITEM,']//',$ele)"/>
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO20" match="*[@FORM='D20'][not(*:AWARD_CONTRACT)]">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R996'"/>
			<xsl:with-param name="content" select="'D20 form and missing AWARD_CONTRACT'"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template mode="DEVCO20" match="*[@FORM='D20']/*:COMPLEMENTARY_INFO">
		<xsl:variable name="ele" select="'ADDRESS_REVIEW_BODY'"/>
		<xsl:if test="not (.//*[matches(local-name(),$ele)])">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R996'"/>
				<xsl:with-param name="content" select="concat('D20 form and missing COMPLEMENTARY_INFO/',$ele)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<xsl:template name="forms">
		<xsl:param name="path"/>
		<xsl:choose>
			<xsl:when test="$path[matches(  local-name(), 'OBJECT_DESCR|OBJECT_CONTRACT|AWARD_CONTRACT')]">
				<xsl:variable name="ele" select="concat($path/ancestor::*[@FORM]/@FORM,' form and ',$path/local-name(), '[',@ITEM,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R997'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$path/ancestor::*:OBJECT_DESCR">
				<xsl:variable name="ele" select="concat($path/ancestor::*[@FORM]/@FORM,' form and OBJECT_CONTRACT[',$path/ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',$path/ancestor::*:OBJECT_DESCR/@ITEM,']//',$path/local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R997'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$path/ancestor::*:OBJECT_CONTRACT">
				<xsl:variable name="ele" select="concat($path/ancestor::*[@FORM]/@FORM,' form and OBJECT_CONTRACT[',$path/ancestor::*:OBJECT_CONTRACT/@ITEM,']//',$path/local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R997'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="$path/ancestor::*:AWARD_CONTRACT">
				<xsl:variable name="ele" select="concat($path/ancestor::*[@FORM]/@FORM,' form and AWARD_CONTRACT[',$path/ancestor::*:AWARD_CONTRACT/@ITEM,']//',$path/local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R997'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<!--choice 1: to display the element name-->
				<xsl:variable name="ele" select="concat($path/ancestor::*[@FORM]/@FORM,' form and ',$path/local-name())"/>
				<!--choice 2: to display the full xpath of the element-->
				<!--<xsl:for-each select="$path/ancestor-or-self::*">
					<xsl:value-of select="concat('/',local-name())"/>
					<xsl:if test="(preceding-sibling::*|following-sibling::*)[local-name()=local-name(current())]">
						<xsl:value-of select="concat('[',count(preceding-sibling::*[local-name()=local-name(current())])+1,']')"/>
					</xsl:if>
				</xsl:for-each>-->
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R997'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
