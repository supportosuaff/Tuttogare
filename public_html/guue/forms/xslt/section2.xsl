<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section2
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 08/06/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*[@FORM][.//*:NOTICE[@TYPE!='PRI_ONLY' and @TYPE!='PER_ONLY']][count(*:OBJECT_CONTRACT)>1]" mode="R314R315">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', *:NOTICE/@*,' and number(OBJECT_CONTRACT)=',count(./*:OBJECT_CONTRACT))"/>
		<xsl:choose>
			<xsl:when test="@FORM='F01' or @FORM='F21'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R314'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="@FORM='F04' or @FORM='F22'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R315'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][.//*:LEGAL_BASIS/@*='32014L0023' and .//*:TYPE_CONTRACT/@*='SUPPLIES']" mode="R316">
		<xsl:variable name="ele" select="'LEGAL_BASIS/@VALUE=32014L0023 and TYPE_CONTRACT/@CTYPE=SUPPLIES'"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R316'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:OBJECT_CONTRACT[1]/*:SHORT_DESCR" mode="R317">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R317'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]/*:VAL_ESTIMATED_TOTAL" mode="R318R319">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='AWARD_CONTRACT' and @TYPE!='QSU_ONLY' and @TYPE!='QSU_CALL_COMPETITION'] "/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R318'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='QSU_ONLY' or @TYPE='QSU_CALL_COMPETITION']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R319'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:OBJECT_CONTRACT[1]/*:LOT_DIVISION" mode="R320">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R320'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:LOT_ALL|*:LOT_MAX_NUMBER|*:LOT_ONE_ONLY" mode="R321R322R324">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F01' and @FORM!='F04' and @FORM!='F21' and @FORM!='F22' and @FORM!='F23']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='PRI_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R321'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='PER_ONLY']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R322'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='CONCESSION_AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R324'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]//*:LOT_ALL|*:OBJECT_CONTRACT[1]//*:LOT_MAX_NUMBER|*:OBJECT_CONTRACT[1]//*:LOT_ONE_ONLY" mode="R323">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R323'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]//*:LOT_MAX_ONE_TENDERER" mode="R325R326">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22' and @FORM!='F23']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='AWARD_CONTRACT' and @TYPE!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R325'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='CONCESSION_AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R326'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]//*:LOT_COMBINING_CONTRACT_RIGHT" mode="R327R328">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22' and @FORM!='F23']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='AWARD_CONTRACT' and @TYPE!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R327'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='CONCESSION_AWARD_CONTRACT']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R328'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT/*:VAL_TOTAL|*:OBJECT_CONTRACT/*:VAL_RANGE_TOTAL" mode="R329">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22' and @FORM!='F23']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='AWARD_CONTRACT' or @TYPE='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R329'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F03' or @FORM='F06' or @FORM='F25'] |*[@FORM][*:NOTICE/@*[matches(.,'AWARD')]]" mode="R331R332">
		<xsl:variable name="ele1" select="'AWARDED_CONTRACT and missing OBJECT_CONTRACT[1]/VAL_(RANGE_)TOTAL'"/>
		<xsl:variable name="ele2" select="'OBJECT_CONTRACT[1]/VAL_(RANGE_)TOTAL'"/>
		<xsl:choose>
			<xsl:when test=".//*:AWARDED_CONTRACT and .//*:OBJECT_CONTRACT[1][not(*:VAL_TOTAL or *:VAL_RANGE_TOTAL)]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R332'"/>
					<xsl:with-param name="content" select="$ele1"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="not(.//*:AWARDED_CONTRACT) and .//*:OBJECT_CONTRACT[1][*:VAL_TOTAL or *:VAL_RANGE_TOTAL]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R331'"/>
					<xsl:with-param name="content" select="$ele2"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][.//*:NOTICE[@TYPE='QSU_ONLY' or @TYPE='QSU_CALL_COMPETITION']][count(.//*:OBJECT_CONTRACT[1]/*:OBJECT_DESCR)>1]" mode="R333">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', .//*:NOTICE/@*,' and number(OBJECT_DESCR)=', count(.//*:OBJECT_CONTRACT[1]/*:OBJECT_DESCR))"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R333'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_DESCR[parent::*//*:LOT_DIVISION][not(.//*:CPV_ADDITIONAL)]" mode="R334">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'P.._ONLY'))] and parent::*:OBJECT_CONTRACT/@ITEM>1"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',parent::*/@ITEM,']/LOT_DIVISION and missing OBJECT_DESCR[',@ITEM,']/CPV_ADDITIONAL')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R334'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:AC[ancestor::*[@FORM]//*:NOTICE[@TYPE='PRI_ONLY' or @TYPE='PER_ONLY']]" mode="R335R336">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/AC')"/>
		<xsl:choose>
			<xsl:when test="ancestor::*/@FORM='F01'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R335'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*/@FORM='F04'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R336'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[*:AC][ancestor::*[@FORM='F15']]" mode="R337">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS/@*='32014L0023' and local-name()='DIRECTIVE_2014_23_EU'"/>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS/@*='32014L0024' and local-name()='DIRECTIVE_2014_24_EU'"/>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS/@*='32014L0025' and local-name()='DIRECTIVE_2014_25_EU'"/>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS/@*='32009L0081' and local-name()='DIRECTIVE_2009_81_EC'"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=', ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/', local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R337'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:VAL_OBJECT[ancestor::*[@FORM]//*:NOTICE]" mode="R338R339R340R342">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R338'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R339'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R340'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='CONCESSION_AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R342'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22']//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR[1]/*:VAL_OBJECT" mode="R341">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*!='QSU_ONLY' and @*!='QSU_CALL_COMPETITION']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R341'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DURATION|*:DATE_START|*:DATE_END" mode="R343R344R345R346">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F07'] or parent::*:QS"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY' and ancestor::*[@FORM='F01']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R343'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY' and ancestor::*[@FORM='F04']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R344'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY' and ancestor::*[@FORM='F21']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R345'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY' and ancestor::*[@FORM='F22'] ">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R346'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR[1]//*[self::*:DURATION|self::*:DATE_START|self::*:DATE_END]" mode="R347">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F07'] or parent::*:QS"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R347'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1]//*[self::*:DURATION|self::*:DATE_START|self::*:DATE_END]" mode="R348">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F07'] or parent::*:QS"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='AWARD_CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R348'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F23' or @FORM='F25']//*:AWARD_CONTRACT[./*:AWARDED_CONTRACT]" mode="R349">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*!='CONCESSION_AWARD_CONTRACT'"/>
			<xsl:when test="*:LOT_NO and ancestor::*[@FORM]//*:NO_LOT_DIVISION"/>
			<xsl:when test="not(*:LOT_NO) and ancestor::*[@FORM]//*:LOT_DIVISION"/>
			<xsl:when test="ancestor::*[@FORM]//*:OBJECT_CONTRACT[.//*:LOT_DIVISION][count(.//*:OBJECT_DESCR) != count(distinct-values(.//*:LOT_NO/normalize-space()))]"/>
			<xsl:when test="not(*:LOT_NO) and ancestor::*[@FORM]//*:OBJECT_DESCR[not(*:DURATION|*:DATE_START|*:DATE_END)]">
				<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',@ITEM,']/AWARDED_CONTRACT and OBJECT_DESCR[missing {DURATION, DATE_START...}]')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R349'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:variable name="lot" select="*:LOT_NO/normalize-space()"/>
				<xsl:variable name="object_item" select="ancestor::*[@FORM]//*:OBJECT_DESCR[*:LOT_NO/normalize-space()=$lot]/@ITEM"/>
				<xsl:if test="ancestor::*[@FORM]//*:OBJECT_DESCR[*:LOT_NO/normalize-space()=$lot][not(*:DURATION|*:DATE_START|*:DATE_END)]">
					<xsl:variable name="ele" select="concat('AWARD_CONTRACT[',@ITEM,'][LOT_NO=', &quot;'&quot;, $lot,&quot;'&quot;,' and AWARDED_CONTRACT] and OBJECT_DESCR[',$object_item,'][LOT_NO=',&quot;'&quot;,$lot,&quot;'&quot;,' and missing {DURATION, DATE_START...}]')"/>
					<xsl:call-template name="msg">
						<xsl:with-param name="rule" select="'R349'"/>
						<xsl:with-param name="content" select="$ele"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][*:NOTICE[@*!='PER_ONLY' and @*!='QSU_ONLY' and @*!='QSU_CALL_COMPETITION']]//*:OBJECT_CONTRACT[1]//*:QS/child::*" mode="R350">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',ancestor::*:OBJECT_DESCR/@ITEM,']/QS/',local-name())"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R350'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<xsl:template match="*[@FORM='F22'][*:NOTICE[@*='PER_ONLY']]//*:QS/child::*" mode="R350">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',ancestor::*:OBJECT_DESCR/@ITEM,']/QS/',local-name())"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R350'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F22'][*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']]//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR[1]" mode="R351">
		<xsl:if test="not(*:QS)">
			<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and missing {QS/DURATION, QS/DATE_START, QS/DATE_END} (OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',@ITEM,'])')"/>
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R351'"/>
				<xsl:with-param name="content" select="$ele"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F02' or @FORM='F05'][.//*:PT_OPEN]//*[matches(local-name(),'CANDIDATE$')]" mode="R352">
		<xsl:choose>
			<xsl:when test="not(descendant-or-self::*[matches(text()[1],'[\p{L}\p{N}]+')])"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('PT_OPEN and OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R352'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04'][*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY']]//*:ACCEPTED_VARIANTS" mode="R353R354">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R353'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R354'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04'][*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY']]//*[matches(local-name(),'^OPTIONS')]" mode="R355R356">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R355'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R356'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21'][*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY']]//*[matches(local-name(),'EU_PROGR')]" mode="R357R358">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',parent::*/@ITEM,']/',local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F01' or @FORM='F21']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R357'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM='F04']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R358'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21']//*:OBJECT_CONTRACT[1]//*:OBJECT_DESCR[not(.//*[matches(local-name(),'EU_PROGR')])]" mode="R359R360R361">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and missing (NO_)EU_PROGR_RELATED (OBJECT_CONTRACT[',parent::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',@ITEM,'])')"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'REDUCING_TIME_LIMITS')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R359'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'CALL_COMPETITION')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R360'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R361'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21' or @FORM='F22']//*:OBJECT_CONTRACT[1]//*:DATE_PUBLICATION_NOTICE" mode="R362R363R364">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and OBJECT_CONTRACT[1]/', local-name())"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'QSU')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R364'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'CALL_COMPETITION')]">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R362'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='CONTRACT'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R362'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='AWARD_CONTRACT']">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R363'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04' or @FORM='F21' or @FORM='F22'][*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY']]//*:OBJECT_CONTRACT[not(.//*:DATE_PUBLICATION_NOTICE)]" mode="R365R366">
		<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and missing DATE_PUBLICATION_NOTICE')"/>
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PRI_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R365'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*='PER_ONLY'">
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R366'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F01' or @FORM='F04']//*:OBJECT_CONTRACT[1][not(.//*:DATE_PUBLICATION_NOTICE)]" mode="R367">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'REDUCING_TIME_LIMITS'))]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and missing DATE_PUBLICATION_NOTICE')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R367'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[*:NO_LOT_DIVISION]/*:OBJECT_DESCR/*[ self::*:TITLE|self::*:LOT_NO ]" mode="R368">
		<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/NO_LOT_DIVISION and OBJECT_DESCR[',parent::*/@ITEM,']/', local-name())"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R368'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[*:LOT_DIVISION]/*:OBJECT_DESCR[ not(*:LOT_NO) ]" mode="R369">
		<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/LOT_DIVISION and missing OBJECT_DESCR[',@ITEM,']/LOT_NO')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R369'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[*:SHORT_DESCR]" mode="R370">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'P.._ONLY'))] and @ITEM>1"/>
			<xsl:when test="*:SHORT_DESCR[descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,']/SHORT_DESCR')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R370'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_DESCR[*:SHORT_DESCR]" mode="R371">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@*='QSU_ONLY' or @*='QSU_CALL_COMPETITION'] and @ITEM>1"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'P.._ONLY'))] and parent::*:OBJECT_CONTRACT/@ITEM>1"/>
			<xsl:when test="*:SHORT_DESCR[descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',parent::*/@ITEM,']/OBJECT_DESCR[',@ITEM,']/SHORT_DESCR')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R371'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[.//*:LOT_COMBINING_CONTRACT_RIGHT]" mode="R372">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'QSU|AWARD_CONTRACT')] "/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'P.._ONLY'))] and @ITEM>1"/>
			<xsl:when test=".//*:LOT_COMBINING_CONTRACT_RIGHT/descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,']/LOT_COMBINING_CONTRACT_RIGHT empty')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R372'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_DESCR[.//*:AC_PRICE/*:AC_WEIGHTING and not(.//*:AC_QUALITY)]" mode="R373">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'P.._ONLY')] "/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'P.._ONLY'))] and parent::*:OBJECT_CONTRACT/@ITEM>1"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',parent::*/@ITEM,']/OBJECT_DESCR[',@ITEM,']//AC_PRICE/AC_WEIGHTING')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R373'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_DESCR[.//*:RENEWAL_DESCR]" mode="R374">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F02' and @FORM!='F05' and @FORM!='D02' ]"/>
			<xsl:when test=".//*:RENEWAL_DESCR/descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_DESCR[',@ITEM,']/RENEWAL_DESCR empty')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R374'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_DESCR[.//*:OPTIONS_DESCR]" mode="R375">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F01' or @FORM='F04' ]"/>
			<xsl:when test=".//*:OPTIONS_DESCR/descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_DESCR[',@ITEM,']/OPTIONS_DESCR empty')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R375'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:TITLE[ parent::*:OBJECT_CONTRACT]" mode="R376">
		<xsl:choose>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',parent::*:OBJECT_CONTRACT/@ITEM,']/TITLE')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R376'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[.//*:LOT_DIVISION][count(.//*:OBJECT_DESCR) != count(distinct-values(.//*:LOT_NO/normalize-space()))]" mode="R377">
		<xsl:choose>
			<xsl:when test=".//*:OBJECT_DESCR[not(*:LOT_NO)]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,'][Total LOT_NO: ',count(.//*:OBJECT_DESCR),' / Unique LOT_NO: ',count(distinct-values(.//*:LOT_NO/normalize-space())),']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R377'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[1][.//*:LOT_MAX_NUMBER][number(.//*:LOT_MAX_NUMBER)>count(.//*:OBJECT_DESCR)]" mode="R378">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'AWARD|QSU|PRI_ONLY|PER_ONLY')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,'][Number of lots: ',count(.//*:OBJECT_DESCR),', LOT_MAX_NUMBER: ',.//*:LOT_MAX_NUMBER,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R378'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:OBJECT_CONTRACT[.//*:LOT_MAX_ONE_TENDERER]" mode="R380">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'AWARD|QSU')]"/>
			<xsl:when test="not(ancestor::*[@FORM]//*:NOTICE/@*[matches(.,'PRI_ONLY|PER_ONLY')]) and  number(.//*:LOT_MAX_ONE_TENDERER)>number(.//*:LOT_MAX_NUMBER)">
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,'][LOT_MAX_NUMBER: ',.//*:LOT_MAX_NUMBER,', LOT_MAX_ONE_TENDERER: ',.//*:LOT_MAX_ONE_TENDERER,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R380'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:when test="number(.//*:LOT_MAX_ONE_TENDERER)>count(.//*:OBJECT_DESCR)">
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',@ITEM,'][Number of lots: ',count(.//*:OBJECT_DESCR),', LOT_MAX_ONE_TENDERER: ',.//*:LOT_MAX_ONE_TENDERER,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R380'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:EU_PROGR_RELATED" mode="R382">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='D01' or @FORM='F01' or @FORM='F04' or @FORM='F21'][*:NOTICE[@*='PRI_ONLY' or @*='PER_ONLY']]"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('OBJECT_CONTRACT[',ancestor::*:OBJECT_CONTRACT/@ITEM,']/OBJECT_DESCR[',ancestor::*:OBJECT_DESCR/@ITEM,']/',local-name())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R382'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][not(.//*:ESSENTIAL_ASSETS)]" mode="R379">
		<xsl:choose>
			<xsl:when test="not(@FORM='T02')"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R379'"/>
					<xsl:with-param name="content" select="'ESSENTIAL_ASSETS missing'"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:ESSENTIAL_ASSETS/*[*:P]" mode="R383">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]/@FORM='T02')"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="local-name()"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R383'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:MAIN_SITE" mode="R384">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T01' or @FORM='T02'])"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="local-name()"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R384'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CATEGORY" mode="R385">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='T02'])"/>
			<xsl:when test="matches(text(),'^01$')"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(),'=',&quot;'&quot;,text(),&quot;'&quot;)"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R385'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
