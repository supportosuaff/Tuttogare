<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section4
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 22/03/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*:PROCEDURE[child::*]" mode="R500R501">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R500'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R501'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:PROCEDURE/*[matches(local-name(),'^PT_')]" mode="R502R503R504R505">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F01']//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R502'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM='F04']//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R503'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM='F23']//*:NOTICE[@*='PRI']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R502'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'REDUCING_TIME_LIMITS')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R504'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'QSU')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R505'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F21' or @FORM='F22'][.//*:NOTICE/@*!='AWARD_CONTRACT']//*:PT_AWARD_CONTRACT_WITHOUT_CALL" mode="R506">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R506'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F15']" mode="R507">
		<xsl:choose>
			<xsl:when test=".//*:LEGAL_BASIS/@*='32014L0023' and .//*:PROCEDURE/*:DIRECTIVE_2014_23_EU"/>
			<xsl:when test=".//*:LEGAL_BASIS/@*='32014L0024' and .//*:PROCEDURE/*:DIRECTIVE_2014_24_EU"/>
			<xsl:when test=".//*:LEGAL_BASIS/@*='32014L0025' and .//*:PROCEDURE/*:DIRECTIVE_2014_25_EU"/>
			<xsl:when test=".//*:LEGAL_BASIS/@*='32009L0081' and .//*:PROCEDURE/*:DIRECTIVE_2009_81_EC"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=', .//*:LEGAL_BASIS/@*,' and PROCEDURE/',local-name(.//*:PROCEDURE/*[matches(local-name(.),'DIRECTIVE_')]))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R507'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04'][not(.//*[matches(local-name(),'^PT_')])]//*:NOTICE/@*[matches(.,'CALL_COMPETITION')]" mode="R508">
		<xsl:variable name="ss_ele">
			<xsl:choose>
				<xsl:when test="ancestor::*[@FORM='F04']">missing {PT_RESTRICTED, PT_NEGOTIATED_WITH_PRIOR_CALL}</xsl:when>
				<xsl:otherwise>missing {PT_RESTRICTED, PT_COMPETITIVE_NEGOTIATION}</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',.,' and ',$ss_ele)"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R508'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][not(.//*[matches(local-name(),'^PT_')])]//*:NOTICE[@*='CONCESSION_AWARD_CONTRACT']" mode="R509">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',@*,' and missing {PT_AWARD_CONTRACT_WITH_PRIOR_PUBLICATION, PT_AWARD_CONTRACT_WITHOUT_PUBLICATION}')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R509'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:FRAMEWORK|*:DPS" mode="R510R511">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F01' and @FORM!='F04']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*!='PRI_ONLY' and @*!='PER_ONLY']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R510'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R511'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(.//*:PT_RESTRICTED) and .//*:DPS]" mode="R512">
		<xsl:choose>
			<xsl:when test="@FORM!='F01' and @FORM!='F02' and @FORM!='F04' and @FORM!='F05'"/>
			<xsl:when test=".//*[matches(local-name(),'^PT_')]">
				<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and DPS')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R512'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'DPS'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R512'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(.//*:CENTRAL_PURCHASING) and .//*:DPS_ADDITIONAL_PURCHASERS]" mode="R513">
		<xsl:variable name="ele" select="'DPS_ADDITIONAL_PURCHASERS'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R513'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(.//*:PT_RESTRICTED) and .//*:DPS]" mode="R514">
		<xsl:choose>
			<xsl:when test="@FORM!='F03' and @FORM!='F06'"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and DPS')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R514'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F21' or @FORM='F22'][.//*:NOTICE/@*='AWARD_CONTRACT']//*:FRAMEWORK/*:JUSTIFICATION" mode="R515">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and FRAMEWORK/JUSTIFICATION')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R515'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:FRAMEWORK/*:JUSTIFICATION" mode="R516">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and FRAMEWORK/JUSTIFICATION')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R516'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02' or @FORM='F05'][(.//*:PT_RESTRICTED| .//*:PT_OPEN) and .//*:REDUCTION_RECOURSE]" mode="R517R518">
		<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and REDUCTION_RECOURSE')"/>
		<xsl:choose>
			<xsl:when test=".//*:PT_OPEN">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R517'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:PT_RESTRICTED">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R518'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02'][not(.//*:PT_COMPETITIVE_NEGOTIATION) and .//*:RIGHT_CONTRACT_INITIAL_TENDERS]" mode="R519">
		<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and RIGHT_CONTRACT_INITIAL_TENDERS')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R519'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04'][.//*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY'] and .//*:EAUCTION_USED]" mode="R520R521">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and EAUCTION_USED')"/>
		<xsl:choose>
			<xsl:when test=".//*:NOTICE[@*='PRI_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R520'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE[@*='PER_ONLY']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R521'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F24' or @FORM='F25'][.//*:TYPE_CONTRACT/@*='SERVICES' and .//*[matches(local-name(),'CONTRACT_COVERED_GPA')]]" mode="R522">
		<xsl:variable name="ele" select="concat('TYPE_CONTRACT[@CTYPE=', .//*:TYPE_CONTRACT/@CTYPE,'] and ',local-name(.//*[matches(local-name(),'CONTRACT_COVERED_GPA')]),'[@CTYPE=WORKS]')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R522'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F24' or @FORM='F25'][.//*:TYPE_CONTRACT/@*='WORKS' and not(.//*[matches(local-name(),'CONTRACT_COVERED_GPA')])]" mode="R523">
		<xsl:variable name="ele" select="concat('TYPE_CONTRACT[@CTYPE=', .//*:TYPE_CONTRACT/@CTYPE,'] and missing (NO_)CONTRACT_COVERED_GPA[@CTYPE=WORKS]')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R523'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION'] and .//*:URL_NATIONAL_PROCEDURE]" mode="R524">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and URL_NATIONAL_PROCEDURE')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R524'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:MAIN_FEATURES_AWARD" mode="R525">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R525'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][.//*:NOTICE[@*='PRI']]//*:MAIN_FEATURES_AWARD" mode="R526">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R526'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_RECEIPT_TENDERS|*:LANGUAGES|*:DATE_AWARD_SCHEDULED" mode="R527R528">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F01' and @FORM!='F04']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*!='PRI_ONLY' and @*!='PER_ONLY']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R527'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PER_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R528'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][.//*:NOTICE[@*='PRI'] and .//*:NOTICE_NUMBER_OJ]" mode="R529">
		<xsl:variable name="ele" select="'NOTICE/@TYPE=PRI and NOTICE_NUMBER_OJ'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R529'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][.//*:DATE_RECEIPT_TENDERS]" mode="R530R531R532R533">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and DATE_RECEIPT_TENDERS')"/>
		<xsl:choose>
			<xsl:when test=".//*:NOTICE/@*='PRI_REDUCING_TIME_LIMITS'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R530'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*='AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R531'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*[matches(.,'QSU')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R532'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*='CONCESSION_AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R533'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21' or @FORM='F22'][not(.//*:DATE_RECEIPT_TENDERS)]//*:NOTICE[@*[matches(.,'P.._CALL_|^CONTRACT')]] " mode="R534">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', @*,' and missing DATE_RECEIPT_TENDERS')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R534'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][not(.//*:DATE_RECEIPT_TENDERS) and .//*:NOTICE[@*='PRI']]" mode="R535">
		<xsl:variable name="ele" select="'NOTICE/@TYPE=PRI and missing DATE_RECEIPT_TENDERS'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R535'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02' or @FORM='F05' or @FORM='F12'][.//*:DATE_DISPATCH_INVITATIONS and .//*:PT_OPEN]" mode="R536R537">
		<xsl:variable name="ele" select="'PT_OPEN and DATE_DISPATCH_INVITATIONS'"/>
		<xsl:choose>
			<xsl:when test="@FORM='F12'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R537'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R536'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][.//*:NOTICE and .//*:LANGUAGES]" mode="R538R539R540R541">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and LANGUAGES')"/>
		<xsl:choose>
			<xsl:when test=".//*:NOTICE/@*[matches(.,'REDUCING_TIME_LIMITS')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R538'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*='AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R539'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="@FORM!='F07' and .//*:NOTICE/@*[matches(.,'QSU')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R540'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*='CONCESSION_AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R541'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21' or @FORM='F22'][not(.//*:LANGUAGES)]//*:NOTICE[@*[matches(.,'P.._CALL_|^CONTRACT')]] " mode="R542">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', @*,' and missing LANGUAGES')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R542'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23'][not(.//*:LANGUAGES) and .//*:NOTICE[@*='PRI']]" mode="R543">
		<xsl:variable name="ele" select="'NOTICE/@TYPE=PRI and missing LANGUAGES'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R543'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F21' or @FORM='F22'][.//*:DATE_AWARD_SCHEDULED and .//*:NOTICE[@*[matches(.,'CONTRACT|^QSU')]]] " mode="R544R545R546">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',.//*:NOTICE/@*,' and DATE_AWARD_SCHEDULED')"/>
		<xsl:choose>
			<xsl:when test=".//*:NOTICE/@*='CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R544'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*='AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R545'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test=".//*:NOTICE/@*[matches(.,'QSU')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R546'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02' or @FORM='F05'][.//*:OPENING_CONDITION and not(.//*:PT_OPEN)] " mode="R547">
		<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and OPENING_CONDITION')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R547'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02' or @FORM='F05'][not(.//*:OPENING_CONDITION) and .//*:PT_OPEN] " mode="R548">
		<xsl:variable name="ele" select="'PT_OPEN and missing OPENING_CONDITION'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R548'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F03' or @FORM='F06'][not(.//*:DPS) and .//*:TERMINATION_DPS] " mode="R549">
		<xsl:variable name="ele" select="'TERMINATION_DPS without DPS'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R549'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F21'][.//*:TERMINATION_PIN and .//*:NOTICE[@*[matches(.,'CALL|^CONTRACT')]]] " mode="R550">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',.//*:NOTICE/@*,' and TERMINATION_PIN')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R550'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:TERMINATION_PIN and .//*:NOTICE[@*[matches(.,'CALL|^CONTRACT')]]] " mode="R551">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',.//*:NOTICE/@*,' and TERMINATION_PIN')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R551'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][.//*:TERMINATION_PIN and .//*:NOTICE[@*='QSU_ONLY']] " mode="R552">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',.//*:NOTICE/@*,' and TERMINATION_PIN')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R552'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:ACCELERATED_PROC" mode="R553">
		<xsl:choose>
			<xsl:when test="descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'ACCELERATED_PROC empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R553'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CRITERIA_EVALUATION" mode="R554">
		<xsl:choose>
			<xsl:when test="descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="'CRITERIA_EVALUATION empty'"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R554'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="//*[@FORM][number(replace(.//*:DATE_RECEIPT_TENDERS/text(),'-','')) &lt;= number(replace(.//*:DATE_DISPATCH_NOTICE/text(),'-',''))]" mode="R555">
		<xsl:choose>
			<xsl:when test=".//*:NOTICE[@*[matches(.,'AWARD|QSU|ONLY|TIME')]]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('DATE_RECEIPT_TENDERS=',.//*:DATE_RECEIPT_TENDERS/text(),', DATE_DISPATCH_NOTICE=',.//*:DATE_DISPATCH_NOTICE/text())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R555'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:NOTICE_NUMBER_OJ[starts-with(.,'19')]" mode="R556">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='PRI' or @*='PRI_ONLY' or @*='PER_ONLY']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(.),'=',text())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R556'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_RECEIPT_TENDERS" mode="R557">
		<xsl:choose>
			<!--		<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY'"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY'"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_REDUCING_TIME_LIMITS'"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='AWARD_CONTRACT'"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'QSU')]"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='CONCESSION_AWARD_CONTRACT'"/>-->
			<xsl:when test="not(matches(normalize-space(text()),'^20'))">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R557'"/>
					<xsl:with-param name="content" select="concat(local-name(.),'=', &quot;'&quot;, normalize-space(./text()), &quot;'&quot;)"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_OPENING_TENDERS" mode="R558">
		<xsl:choose>
			<!--<xsl:when test="ancestor::*[@FORM]//*:PT_OPEN"/>-->
			<xsl:when test="not(matches(normalize-space(text()),'^20'))">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R558'"/>
					<xsl:with-param name="content" select="concat(local-name(.),'=', &quot;'&quot;, normalize-space(./text()), &quot;'&quot;)"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[not(.//*:DATE_RECEIPT_TENDERS)]//*:NOTICE[@*='PER_REDUCING_TIME_LIMITS']" mode="R559">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', @*,' and missing DATE_RECEIPT_TENDERS')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R559'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F03' or @FORM='F06' or @FORM='F25' or .//*:NOTICE/@*[matches(.,'AWARD')]][not(.//*:NOTICE_NUMBER_OJ)]" mode="R560">
		<xsl:choose>
			<xsl:when test="not(.//*:PROCEDURE/*[matches(local-name(),'^PT_')])"/>
			<xsl:when test=".//*:PROCEDURE/*[matches(local-name(),'^PT_.*WITHOUT_')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(.//*[matches(local-name(),'^PT_')]),' and missing NOTICE_NUMBER_OJ')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R560'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(*:PROCEDURE)]" mode="R561">
		<xsl:choose>
			<xsl:when test="not(@FORM='T01')"/>
			<xsl:otherwise>
			<xsl:variable name="ele" select="concat(@FORM,' form and PROCEDURE missing')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R561'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
